<?php

use CloudinaryExtension\Image\Transformation;
use CloudinaryExtension\Image\Transformation\Freeform;

class Cloudinary_Cloudinary_Adminhtml_CloudinaryajaxController extends Mage_Adminhtml_Controller_Action
{
    public function sampleAction()
    {
        try {
            if (!$this->_validateSecretKey()) {
                throw new Exception('Incorrect security key');
            }

            $freeTransform = $this->getRequest()->getParam('free');
            $freeModel = Mage::getModel('cloudinary_cloudinary/system_config_free');
            $url = $freeModel->sampleImageUrl($freeModel->defaultTransform($freeTransform));
            $this->validate($freeModel, $url);
            $this->jsonResponse(
                200,
                array('url' => $url)
            );
        } catch (\Exception $e) {
            $this->jsonResponse(401, array('error' => $e->getMessage()));
        }
    }

    public function imageAction()
    {
        try {
            $freeModel = Mage::getModel('cloudinary_cloudinary/system_config_free');

            $url = $freeModel->namedImageUrl(
                $this->getRequest()->getParam('image'),
                $freeModel->defaultTransform($this->getRequest()->getParam('free'))
            );

            $this->validate($freeModel, $url);

            $this->jsonResponse(
                200,
                array('url' => $url)
            );
        } catch (\Exception $e) {
            $this->jsonResponse(401, array('error' => $e->getMessage()));
        }
    }

    /**
     * @param int $code
     * @param array $payload
     */
    private function jsonResponse($code, array $payload)
    {
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-type', 'application/json')
            ->setHttpResponseCode($code)
            ->setBody(Mage::helper('core')->jsonEncode($payload));
    }

    /**
     * @param Cloudinary_Cloudinary_Model_System_Config_Free $model
     * @param string $url
     * @throws Exception
     */
    private function validate(Cloudinary_Cloudinary_Model_System_Config_Free $model, $url)
    {
        if (!$model->hasAccountConfigured()) {
            throw new \Exception('Cloudinary credentials required');
        }

        $model->validateImageUrl($url);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed();
    }
}
