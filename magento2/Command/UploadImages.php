<?php

namespace Cloudinary\Cloudinary\Command;

use Cloudinary\Cloudinary\Model\BatchUploader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UploadImages extends Command
{
    /**
     * @var BatchUploader
     */
    private $batchUploader;

    /**
     * @param BatchUploader $batchUploader
     */
    public function __construct(BatchUploader $batchUploader)
    {
        parent::__construct('cloudinary:upload:all');

        $this->batchUploader = $batchUploader;
    }

    /**
     * Configure the command
     * 
     * @return void
     */
    protected function configure()
    {
        $this->setName('cloudinary:upload:all');
        $this->setDescription('Upload unsynchronised images');
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->batchUploader->uploadUnsynchronisedImages($output);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
