<?php

namespace spec\CloudinaryExtension\Migration;

use CloudinaryExtension\Image\Synchronizable;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\Migration\BatchUploader;
use CloudinaryExtension\Migration\Logger;
use CloudinaryExtension\Migration\MediaResolver;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchUploaderSpec extends ObjectBehavior
{
    function let(
        ImageManager $imageManager,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2)
    {
        $this->beConstructedWith($imageManager, $logger, '/catalog/media');

        $image1->tagAsSynchronized()->willReturn();
        $image2->tagAsSynchronized()->willReturn();
    }

    function it_uploads_and_synchronizes_a_collection_of_images(
        ImageManager $imageManager,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/r/b/image2.jpg');

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageManager->uploadImage('/catalog/media/z/b/image1.jpg')->shouldHaveBeenCalled();
        $imageManager->uploadImage('/catalog/media/r/b/image2.jpg')->shouldHaveBeenCalled();

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/z/b/image1.jpg'))->shouldHaveBeenCalled();
        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/r/b/image2.jpg'))->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 2))->shouldHaveBeenCalled();
    }

    function it_logs_an_error_if_any_of_the_image_uploads_fails(
        ImageManager $imageManager,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/invalid');

        $exception = new \Exception('Invalid file');

        $images = array($image1, $image2);

        $imageManager->uploadImage('/catalog/media/invalid')->willThrow($exception);

        $imageManager->uploadImage('/catalog/media/z/b/image1.jpg')->shouldBeCalled();

        $this->uploadImages($images);

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldNotHaveBeenCalled();

        $logger->error(
            sprintf(BatchUploader::MESSAGE_UPLOAD_ERROR, $exception->getMessage(), '/invalid')
        )->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1))->shouldHaveBeenCalled();
    }
}

