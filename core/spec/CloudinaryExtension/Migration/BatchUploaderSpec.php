<?php

namespace spec\CloudinaryExtension\Migration;

use CloudinaryExtension\Image;
use CloudinaryExtension\Image\Synchronizable;
use CloudinaryExtension\ImageProvider;
use CloudinaryExtension\Migration\BatchUploader;
use CloudinaryExtension\Migration\Logger;
use CloudinaryExtension\Migration\MediaResolver;
use CloudinaryExtension\Migration\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchUploaderSpec extends ObjectBehavior
{

    const MEDIA_PATH = '/catalog/media';

    function let(
        ImageProvider $imageProvider,
        Task $migrationTask,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2)
    {
        $this->beConstructedWith($imageProvider, $migrationTask, $logger, self::MEDIA_PATH);

        $image1->tagAsSynchronized()->willReturn();
        $image2->tagAsSynchronized()->willReturn();
        $migrationTask->hasBeenStopped()->willReturn(false, false);
    }

    function it_uploads_and_synchronizes_a_collection_of_images(
        ImageProvider $imageProvider,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $path1 = '/z/b/image1.jpg';
        $path2 = '/r/b/image2.jpg';
        $relativePath1 = basename($path1);
        $relativePath2 = basename($path2);
        $absolutePath1 = self::MEDIA_PATH . $path1;
        $absolutePath2 = self::MEDIA_PATH . $path2;

        $image1->getFilename()->willReturn($path1);
        $image2->getFilename()->willReturn($path2);
        $image1->getRelativePath()->willReturn($relativePath1);
        $image2->getRelativePath()->willReturn($relativePath2);

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload(Image::fromPath($absolutePath1, $relativePath1))->shouldHaveBeenCalled();
        $imageProvider->upload(Image::fromPath($absolutePath2, $relativePath2))->shouldHaveBeenCalled();

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, "$absolutePath1 - $relativePath1"))->shouldHaveBeenCalled();
        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, "$absolutePath2 - $relativePath2"))->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 2, 0))->shouldHaveBeenCalled();
    }

    function it_logs_an_error_if_any_of_the_image_uploads_fails(
        ImageProvider $imageProvider,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $path1 = '/z/b/image1.jpg';
        $path2 = '/invalid';
        $relativePath1 = basename($path1);
        $relativePath2 = basename($path2);
        $absolutePath1 = self::MEDIA_PATH . $path1;
        $absolutePath2 = self::MEDIA_PATH . $path2;
        $apiImage1 = Image::fromPath($absolutePath1, $relativePath1);
        $apiImage2 = Image::fromPath($absolutePath2, $relativePath2);

        $image1->getFilename()->willReturn($path1);
        $image2->getFilename()->willReturn($path2);
        $image1->getRelativePath()->willReturn($relativePath1);
        $image2->getRelativePath()->willReturn($relativePath2);


        $exception = new \Exception('Invalid file');

        $images = array($image1, $image2);

        $imageProvider->upload($apiImage1)->shouldBeCalled();
        $imageProvider->upload($apiImage2)->willThrow($exception);

        $this->uploadImages($images);

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldNotHaveBeenCalled();

        $logger->error(
            sprintf(BatchUploader::MESSAGE_UPLOAD_ERROR, $exception->getMessage(), "$absolutePath2 - $relativePath2")
        )->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1, 1))->shouldHaveBeenCalled();
    }


    function it_stops_the_upload_process_if_task_is_stopped(
        ImageProvider $imageProvider,
        Task $migrationTask,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $path1 = '/z/b/image1.jpg';
        $path2 = '/invalid';
        $relativePath1 = basename($path1);
        $relativePath2 = basename($path2);
        $absolutePath1 = self::MEDIA_PATH . $path1;
        $absolutePath2 = self::MEDIA_PATH . $path2;
        $apiImage1 = Image::fromPath($absolutePath1, $relativePath1);
        $apiImage2 = Image::fromPath($absolutePath2, $relativePath2);

        $image1->getFilename()->willReturn($path1);
        $image2->getFilename()->willReturn($path2);
        $image1->getRelativePath()->willReturn($relativePath1);
        $image2->getRelativePath()->willReturn($relativePath2);

        $migrationTask->hasBeenStopped()->willReturn(false, true);

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload($apiImage1)->shouldHaveBeenCalled();
        $image1->tagAsSynchronized()->shouldHaveBeenCalled();

        $imageProvider->upload($apiImage2)->shouldNotHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldNotHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1, 0))->shouldHaveBeenCalled();
    }
}

