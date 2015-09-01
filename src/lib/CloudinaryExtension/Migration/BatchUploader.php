<?php

namespace CloudinaryExtension\Migration;

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Synchronizable;
use CloudinaryExtension\ImageProvider;

class BatchUploader
{
    const MESSAGE_STATUS = 'Cloudinary migration: %s images migrated, %s failed';

    const MESSAGE_UPLOADED = 'Cloudinary migration: uploaded %s';

    const MESSAGE_UPLOAD_ERROR = 'Cloudinary migration: %s trying to upload %s';

    private $imageProvider;

    private $baseMediaPath;

    private $logger;

    private $migrationTask;

    private $countMigrated = 0;
    private $countFailed = 0;

    public function __construct(ImageProvider $imageProvider, Task $migrationTask, Logger $logger, $baseMediaPath)
    {
        $this->imageProvider = $imageProvider;
        $this->migrationTask = $migrationTask;
        $this->baseMediaPath = $baseMediaPath;
        $this->logger = $logger;
    }

    public function uploadImages(array $images)
    {
        $this->countMigrated = 0;
        foreach ($images as $image) {

            if ($this->migrationTask->hasBeenStopped()) {
                break;
            }
            $this->uploadImage($image);
        }

        $this->logger->notice(sprintf(self::MESSAGE_STATUS, $this->countMigrated, $this->countFailed));
    }

    private function getAbsolutePath(Synchronizable $image)
    {
        return sprintf('%s%s', $this->baseMediaPath, $image->getFilename());
    }

    private function uploadImage(Synchronizable $image)
    {
        $absolutePath = $this->getAbsolutePath($image);
        $relativePath = $image->getRelativePath();
        $apiImage = Image::fromPath($absolutePath, $relativePath);

        try {
            $uploadResult = $this->imageProvider->upload($apiImage);
            $image->tagAsSynchronized();
            $this->countMigrated++;
            $this->_debugLogResult($uploadResult);
            $this->logger->notice(sprintf(self::MESSAGE_UPLOADED, $absolutePath . ' - ' . $relativePath));
        } catch (\Exception $e) {
            $this->countFailed++;
            $this->logger->error(sprintf(self::MESSAGE_UPLOAD_ERROR, $e->getMessage(), $absolutePath . ' - ' . $relativePath));
        }
    }

    /**
     * @param $array the original key we want to select from
     * @param $keys the keys to preserve as an array
     * @return array
     */
    private function _arraySelect($array, $keys)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $array[$key];
        }
        return $result;
    }

    /**
     * @param $uploadResult
     */
    private function _debugLogResult($uploadResult)
    {
        $extractedResult = $this->_arraySelect($uploadResult, ['url', 'public_id']);
        $this->logger->debugLog(json_encode($extractedResult, JSON_PRETTY_PRINT) . "\n ------------------------------------------- \n");
    }


}
