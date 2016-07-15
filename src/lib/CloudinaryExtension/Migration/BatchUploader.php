<?php

namespace CloudinaryExtension\Migration;

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Synchronizable;
use CloudinaryExtension\ImageProvider;

class BatchUploader
{
    const MESSAGE_STATUS = 'Cloudinary migration: %s images migrated';

    const MESSAGE_UPLOADED = 'Cloudinary migration: uploaded %s';

    const MESSAGE_UPLOADED_EXISTS = 'Cloudinary migration: %s exists - tagged as synchronized';

    const MESSAGE_UPLOAD_ERROR = 'Cloudinary migration: %s trying to upload %s';

    private $imageProvider;

    private $baseMediaPath;

    private $logger;

    private $migrationTask;

    private $countMigrated = 0;

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

        $this->logger->notice(sprintf(self::MESSAGE_STATUS, $this->countMigrated));
    }

    private function getAbsolutePath(Synchronizable $image)
    {
        return sprintf('%s%s', $this->baseMediaPath, $image->getFilename());
    }

    private function uploadImage(Synchronizable $image)
    {
        try {
            $this->imageProvider->upload(Image::fromPath($this->getAbsolutePath($image)));
            $image->tagAsSynchronized();
            $this->countMigrated++;
            $this->logger->notice(sprintf(self::MESSAGE_UPLOADED, $image->getFilename()));
        } catch (\Cloudinary\Api\AlreadyExists $e) {
            $image->tagAsSynchronized();
            $this->countMigrated++;
            $this->logger->notice(sprintf(self::MESSAGE_UPLOADED_EXISTS, $image->getFilename()));
        } catch (\Exception $e) {
            $this->logger->error(sprintf(self::MESSAGE_UPLOAD_ERROR, $e->getMessage(), $image->getFilename()));
        }
    }

}
