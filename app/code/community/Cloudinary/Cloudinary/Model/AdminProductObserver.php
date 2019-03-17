<?php

use Varien_Event_Observer as EventObserver;
use Mage_Catalog_Model_Product as Product;

class Cloudinary_Cloudinary_Model_AdminProductObserver extends Mage_Core_Model_Abstract
{
    const CLOUDINARY_FREE_FORM_FIELD = 'cloudinary_free';
    const CLOUDINARY_FREE_UPDATED_FIELD = 'cloudinary_free_updated';

    /**
     * @param Varien_Event_Observer $event
     * @throws Exception
     */
    public function beforeProductSave(EventObserver $event)
    {
        $product = $event->getProduct();
        $post = Mage::app()->getRequest()->getPost();

        if ($product && $post && array_key_exists(self::CLOUDINARY_FREE_FORM_FIELD, $post)) {
            $this->validateFreeTransformValues(
                $this->filterUpdatedImages(
                    $post[self::CLOUDINARY_FREE_FORM_FIELD],
                    $post[self::CLOUDINARY_FREE_UPDATED_FIELD]
                )
            );
            $this->storeFreeTransformFields($post[self::CLOUDINARY_FREE_FORM_FIELD], $product);
        }
    }

    /**
     * @param array $imageData
     * @param array $imageUpdated
     * @return array
     */
    private function filterUpdatedImages(array $imageData, array $imageUpdated)
    {
        return array_filter(
            $imageData,
            function ($id) use ($imageUpdated) {
                return $imageUpdated[$id];
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param array $imageData
     * @throws Exception
     */
    private function validateFreeTransformValues(array $imageData)
    {
        if (!count($imageData)) {
            return;
        }

        $model = Mage::getModel('cloudinary_cloudinary/system_config_free');
        if (!$model->hasAccountConfigured()) {
            throw new \Exception('Cloudinary credentials required');
        }

        foreach ($imageData as $transform) {
            $model->validateImageUrl($model->sampleImageUrl($model->defaultTransform($transform)));
        }
    }

    /**
     * @param array $imageData
     */
    private function storeFreeTransformFields(array $imageData, Mage_Catalog_Model_Product $product)
    {
        $mediaImages = $this->getMediaGalleryImages($product);

        // TODO: Should be removed on future releases
        foreach ($imageData as $id => $freeTransform) {
            Mage::getModel('cloudinary_cloudinary/transformation')
                ->setImageName($this->getImageNameForId($id, $mediaImages))
                ->setFreeTransformation($freeTransform)
                ->save();

            $cloudinaryData = json_decode((string)$product->getCloudinaryData(), true) ?: array();
            $cloudinaryData['transformation'] = (isset($cloudinaryData['transformation']))? (array) $cloudinaryData['transformation'] : array();
            $cloudinaryData['transformation'][md5($this->getImageNameForId($id, $mediaImages))] = (string) $freeTransform;
            $product->setCloudinaryData(json_encode($cloudinaryData));
        }


        /*foreach ($mediaImages as &$image) {
            if (isset($imageData[$image["value_id"]])) {
                $image['cloudinary_transformation'] = $imageData[$image["value_id"]];
            }
        }

        $productPost = Mage::app()->getRequest()->getPost("product");
        $productPost['media_gallery']['images'] = json_encode($mediaImages);
        Mage::app()->getRequest()->setPost("product", $productPost);
        $mediaGallery = $product->getMediaGallery();
        $mediaGallery['images'] = $productPost['media_gallery']['images'];
        $product->setData('media_gallery', $mediaGallery);*/
    }

    /**
     * @param string $id
     * @param array $images
     * @return string
     */
    private function getImageNameForId($id, $images)
    {
        foreach ($images as $image) {
            if ($image['value_id'] == $id) {
                return $image['file'];
            }
        }

        return '';
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    private function getMediaGalleryImages(Mage_Catalog_Model_Product $product)
    {
        return json_decode($product->getMediaGallery()['images'], true);
    }
}
