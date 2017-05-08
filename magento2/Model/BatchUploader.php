<?php

namespace Cloudinary\Cloudinary\Model;

use CloudinaryExtension\CloudinaryImageManager;
use Cloudinary\Cloudinary\Model\Configuration;
use Cloudinary\Cloudinary\Model\ImageRepository;
use Cloudinary\Cloudinary\Model\MigrationTask;
use Symfony\Component\Console\Output\OutputInterface;

class BatchUploader
{
    const ERROR_MIGRATION_ALREADY_RUNNING = 'Cannot start upload - migration already running.';
    const MESSAGE_UPLOAD_IMAGE = 'Uploading image: %s';
    const MESSAGE_UPLOAD_COMPLETE = 'Completed. Images uploaded: %s';

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var MigrationTask
     */
    private $migrationTask;

    /**
     * @var CloudinaryImageManager
     */
    private $cloudinaryImageManager;

    /**
     * @param ImageRepository $imageRepository
     * @param Configuration $configuration
     * @param MigrationTask $migrationTask
     * @param CloudinaryImageManager $cloudinaryImageManager
     */
    public function __construct(
        ImageRepository $imageRepository,
        Configuration $configuration,
        MigrationTask $migrationTask,
        CloudinaryImageManager $cloudinaryImageManager
    ) {
        $this->imageRepository = $imageRepository;
        $this->configuration = $configuration;
        $this->migrationTask = $migrationTask;
        $this->cloudinaryImageManager = $cloudinaryImageManager;
    }

    /**
     * Find unsynchronised images and upload them to cloudinary
     *
     * @param OutputInterface|null $output
     * @return bool
     * @throws \Exception
     */
    public function uploadUnsynchronisedImages(OutputInterface $output = null)
    {
        if ($this->migrationTask->hasStarted()) {
            $this->displayMessage($output, self::ERROR_MIGRATION_ALREADY_RUNNING);
            return false;
        }

        try {
            $this->migrationTask->start();

            $images = $this->imageRepository->findUnsynchronisedImages();
            foreach ($images as $image) {
                $this->displayMessage($output, sprintf(self::MESSAGE_UPLOAD_IMAGE, $image));
                $this->cloudinaryImageManager->uploadAndSynchronise($image);
            }

            $this->migrationTask->stop();
            $this->displayMessage($output, sprintf(self::MESSAGE_UPLOAD_COMPLETE, count($images)));

            return true;

        } catch (\Exception $e) {
            $this->migrationTask->stop();
            throw $e;
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $message
     */
    private function displayMessage(OutputInterface $output, $message)
    {
        if ($output) {
            $output->writeln($message);
        }
    }
}
