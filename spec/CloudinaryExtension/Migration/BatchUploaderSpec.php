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
    function let(
        ImageProvider $imageProvider,
        Task $migrationTask,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2)
    {
        $this->beConstructedWith($imageProvider, $migrationTask, $logger, '/catalog/media');

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
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/r/b/image2.jpg');

        $image1->getRelativePath()->willReturn('');
        $image2->getRelativePath()->willReturn('');

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload(Image::fromPath('/catalog/media/z/b/image1.jpg'))->shouldHaveBeenCalled();
        $imageProvider->upload(Image::fromPath('/catalog/media/r/b/image2.jpg'))->shouldHaveBeenCalled();

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/catalog/media/z/b/image1.jpg' . ' - ' . ''))->shouldHaveBeenCalled();
        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/catalog/media/r/b/image2.jpg' . ' - ' . ''))->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 2, 0))->shouldHaveBeenCalled();
    }

    function it_logs_an_error_if_any_of_the_image_uploads_fails(
        ImageProvider $imageProvider,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/invalid');

        $image1->getRelativePath()->willReturn('');
        $image2->getRelativePath()->willReturn('');

        $exception = new \Exception('Invalid file');

        $images = array($image1, $image2);

        $imageProvider->upload(Image::fromPath('/catalog/media/invalid'))->willThrow($exception);
        $imageProvider->upload(Image::fromPath('/catalog/media/z/b/image1.jpg'))->shouldBeCalled();

        $this->uploadImages($images);

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldNotHaveBeenCalled();

        $logger->error(
            sprintf(BatchUploader::MESSAGE_UPLOAD_ERROR, $exception->getMessage(), ('/catalog/media/invalid' . ' - '. ''))
        )->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1, 1))->shouldHaveBeenCalled();
    }


    function it_rescues_already_exists_errors_and_tags_image_as_synchronized(
        ImageProvider $imageProvider,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');

        $exception = new \Cloudinary\Api\AlreadyExists('Already exists');

        $images = array($image1);

        $imageProvider->upload(Image::fromPath('/catalog/media/z/b/image1.jpg'))->willThrow($exception);

        $this->uploadImages($images);

        $image1->tagAsSynchronized()->shouldHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_UPLOADED, '/z/b/image1.jpg'))->shouldHaveBeenCalled();
    }


    function it_stops_the_upload_process_if_task_is_stopped(
        ImageProvider $imageProvider,
        Task $migrationTask,
        Logger $logger,
        Synchronizable $image1,
        Synchronizable $image2
    ) {
        $image1->getFilename()->willReturn('/z/b/image1.jpg');
        $image2->getFilename()->willReturn('/invalid');

        $image1->getRelativePath()->willReturn('/z/b/image1.jpg');
        $image2->getRelativePath()->willReturn('/invalid');

        $migrationTask->hasBeenStopped()->willReturn(false, true);

        $images = array($image1, $image2);

        $this->uploadImages($images);

        $imageProvider->upload('/catalog/media/z/b/image1.jpg')->shouldHaveBeenCalled();
        $image1->tagAsSynchronized()->shouldHaveBeenCalled();

        $imageProvider->upload('/catalog/media/r/b/image2.jpg')->shouldNotHaveBeenCalled();
        $image2->tagAsSynchronized()->shouldNotHaveBeenCalled();

        $logger->notice(sprintf(BatchUploader::MESSAGE_STATUS, 1, 0))->shouldHaveBeenCalled();
    }
}

