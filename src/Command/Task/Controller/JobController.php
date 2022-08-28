<?php

namespace Macareux\JobRunner\Command\Task\Controller;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Controller\AbstractController;
use Concrete\Core\Command\Task\Input\Definition\Definition;
use Concrete\Core\Command\Task\Input\Definition\SelectField;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\BatchProcessTaskRunner;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Text;
use Macareux\JobRunner\Command\RunJobCommand;
use Macareux\JobRunner\Command\RunJobQueueCommand;
use Macareux\JobRunner\Job\QueueableJob;
use Macareux\JobRunner\Queue\Queue;
use Symfony\Component\Finder\Finder;

class JobController extends AbstractController
{
    public function getName(): string
    {
        return t('Run automated job');
    }

    public function getDescription(): string
    {
        return t('Run automated job');
    }

    public function getInputDefinition(): ?Definition
    {
        $jobs = [];
        foreach ($this->getAvailableList() as $handle => $name) {
            $jobs[$handle] = $name;
        }

        $definition = new Definition();
        $definition->addField(new SelectField(
            'job',
            t('Job to run'),
            t('You must select a job you want to run.'),
            $jobs,
            true
        ));

        return $definition;
    }

    public function getTaskRunner(TaskInterface $task, InputInterface $input): TaskRunnerInterface
    {
        $batch = Batch::create();
        $jobHandle = (string) $input->getField('job')->getValue();
        $job = Job::getJobObjByHandle($jobHandle);
        if ($job instanceof QueueableJob) {
            $queue = new Queue();
            $job->start($queue);
            foreach ($queue->getMessages() as $message) {
                $batch->add(new RunJobQueueCommand($jobHandle, $message));
            }
        } else {
            $batch->add(new RunJobCommand($jobHandle));
        }

        return new BatchProcessTaskRunner($task, $batch, $input, t('Running %s job...', $jobHandle));
    }

    protected function getAvailableList(): \Generator
    {
        /** @var Text $text */
        $text = Application::getFacadeApplication()->make('helper/text');

        // Maybe converted
        $finder = new Finder();
        $finder->files()->in(DIR_FILES_JOBS)->name('*.php')->notContains('use ZendQueue');

        foreach ($finder as $file) {
            $handle = $file->getFilenameWithoutExtension();
            /** @var Job $job */
            $job = Job::getJobObjByHandle($handle);
            yield $handle => $job->getJobName();
        }

        // Not yet converted
        $finder = new Finder();
        $finder->files()->in(DIR_FILES_JOBS)->name('*.php')->contains('use ZendQueue');
        foreach ($finder as $file) {
            $handle = $file->getFilenameWithoutExtension();
            $name = $text->unhandle($handle);
            yield $handle => $name;
        }
    }
}
