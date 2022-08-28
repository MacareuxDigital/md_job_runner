<?php

namespace Macareux\JobRunner\Console\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Console\Command;
use Concrete\Core\File\Service\File;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ConvertQueueableJobCommand extends Command
{
    public function handle(Application $app)
    {
        $search = [
            'Concrete\Core\Job\QueueableJob',
            'ZendQueue\Message',
            'ZendQueue\Queue',
        ];
        $replace = [
            'Macareux\JobRunner\Job\QueueableJob',
            'Macareux\JobRunner\Queue\Message',
            'Macareux\JobRunner\Queue\Queue',
        ];
        $file = $this->input->getArgument('file');
        $revert = $this->input->getOption('revert');

        if (is_file($file)) {
            /** @var File $helper */
            $helper = $app->make('helper/file');
            $contents = $helper->getContents($file);
            if (strpos($contents, 'getJobName(')) {
                if ($revert) {
                    $contents = str_replace($replace, $search, $contents);
                } else {
                    $contents = str_replace($search, $replace, $contents);
                }
                $helper->clear($file);
                $helper->append($file, $contents);
                $this->output->writeln(t('Successfully converted.'));

                return true;
            }
                $this->output->writeln(t('The given file seems not a valid job file.'));
        } else {
            $this->output->writeln(t('The given file seems not exists.'));
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('md:convert-queueable-job')
            ->setDescription('Convert a class of automated job to compatible with Macareux Job Runner package.')
            ->addEnvOption()
            ->addArgument('file', InputArgument::REQUIRED, 'The path of the file to convert')
            ->addOption('revert', 'r', InputOption::VALUE_NONE, 'Try to revert a class to original')
        ;
    }
}
