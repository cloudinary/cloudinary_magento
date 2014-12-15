<?php

namespace CloudinaryExtension\Migration;

use CloudinaryExtension\ImageManager;

class BatchUploader
{
    const MESSAGE_STATUS = 'Cloudinary migration: %s images migrated';

    const MESSAGE_UPLOADED = 'Cloudinary migration: uploaded %s';

    const MESSAGE_UPLOAD_ERROR = 'Cloudinary migration: %s trying to upload %s';

    private $imageManager;

    private $baseMediaPath;

    private $logger;

    private $migrationTask;

    private $countMigrated = 0;

    public function __construct(ImageManager $imageManager, Task $migrationTask, Logger $logger, $baseMediaPath)
    {
        $this->imageManager = $imageManager;
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

        $this->logger->notice(sprintf(self::MESSAGE_STATUS, $this->countMigrated));
    }

    private function getAbsolutePath($image)
    {
        return sprintf('%s%s', $this->baseMediaPath, $image->getFilename());
    }

    private function uploadImage($image)
    {
        try {
            $this->imageManager->uploadImage($this->getAbsolutePath($image));
            $image->tagAsSynchronized();
            $this->countMigrated++;
            $this->logger->notice(sprintf(self::MESSAGE_UPLOADED, $image->getFilename()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf(self::MESSAGE_UPLOAD_ERROR, $e->getMessage(), $image->getFilename()));
        }
    }

}
