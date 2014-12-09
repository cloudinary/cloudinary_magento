<?php

namespace spec\CloudinaryExtension\Migration;

use CloudinaryExtension\Migration\BatchUploader;
use CloudinaryExtension\Migration\Logger;
use CloudinaryExtension\Migration\Queue;
use CloudinaryExtension\Migration\SynchronizedMediaRepository;
use CloudinaryExtension\Migration\Task;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueueSpec extends ObjectBehavior
{
    function let(
        Task $migrationTask,
        SynchronizedMediaRepository $synchronizedMediaRepository,
        BatchUploader $batchUploader,
        Logger $logger
    ) {
        $this->beConstructedWith($migrationTask, $synchronizedMediaRepository, $batchUploader, $logger);
    }

    function it_does_not_process_the_migration_queue_if_task_has_not_been_started(
        Task $migrationTask,
        SynchronizedMediaRepository $synchronizedMediaRepository,
        Logger $logger
    ) {
        $migrationTask->hasStarted()->willReturn(false);

        $synchronizedMediaRepository->findUnsynchronisedImages()->shouldNotBeCalled();
        $logger->notice(Argument::any())->shouldNotBeCalled();

        $this->process();
    }


    function it_processes_the_migration_queue_if_task_has_been_started(
        Task $migrationTask,
        SynchronizedMediaRepository $synchronizedMediaRepository,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $migrationTask->hasStarted()->willReturn(true);
        $migrationTask->stop()->willReturn();

        $logger->notice(Queue::MESSAGE_PROCESSING)->shouldBeCalled();
        $synchronizedMediaRepository->findUnsynchronisedImages()->willReturn(array('image1', 'image2'));

        $batchUploader->uploadImages(array('image1', 'image2'))->shouldBeCalled();

        $this->process();
    }

    function it_stops_the_migration_task_if_there_is_nothing_left_to_process(
        Task $migrationTask,
        SynchronizedMediaRepository $synchronizedMediaRepository,
        Logger $logger,
        BatchUploader $batchUploader
    ) {
        $migrationTask->hasStarted()->willReturn(true);
        $synchronizedMediaRepository->findUnsynchronisedImages()->willReturn(array());

        $logger->notice(Queue::MESSAGE_COMPLETE)->shouldBeCalled();
        $migrationTask->stop()->shouldBeCalled();

        $batchUploader->uploadImages(Argument::any())->shouldNotBeCalled();

        $this->process();
    }
}
