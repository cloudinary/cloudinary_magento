<?php

namespace spec\Cloudinary\Cloudinary\Model;

use Cloudinary\Cloudinary\Model\Configuration;
use Cloudinary\Cloudinary\Model\ImageRepository;
use Cloudinary\Cloudinary\Model\MigrationTask;
use CloudinaryExtension\CloudinaryImageManager;
use CloudinaryExtension\Image;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BatchUploaderSpec extends ObjectBehavior
{
    function let(
        ImageRepository $imageRepository,
        Configuration $configuration,
        MigrationTask $migrationTask,
        CloudinaryImageManager $cloudinaryImageManager
    ) {
        $this->beConstructedWith(
            $imageRepository,
            $configuration,
            $migrationTask,
            $cloudinaryImageManager
        );
    }

    function it_should_not_do_batch_upload_when_migration_task_started(
        OutputInterface $outputInterface,
        MigrationTask $migrationTask
    ) {
        $migrationTask->hasStarted()->willReturn(true);

        $this->uploadUnsynchronisedImages($outputInterface)->shouldBe(false);
    }

    function it_should_upload_and_synchronised_images(
        OutputInterface $outputInterface,
        MigrationTask $migrationTask,
        ImageRepository $imageRepository,
        Image $image
    )
    {
        $image->__toString()->willReturn('pink_image.gif');
        $image->getRelativePath()->willReturn('/p/i/pink_dress.gif');

        $migrationTask->hasStarted()->willReturn(false);

        $migrationTask->start()->shouldBeCalled();
        $migrationTask->stop()->shouldBeCalled();

        $imageRepository->findUnsynchronisedImages()->willReturn([$image]);

        $this->uploadUnsynchronisedImages($outputInterface)->shouldBe(true);
    }
}
