<?php

namespace Concrete\Package\MdJobRunner;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Manager;
use Concrete\Core\Console\Application as ConsoleApplication;
use Concrete\Core\Package\Package;
use Macareux\JobRunner\Command\Task\Controller\JobController;
use Macareux\JobRunner\Console\Command\ConvertQueueableJobCommand;

class Controller extends Package
{
    /**
     * @var string package handle
     */
    protected $pkgHandle = 'md_job_runner';

    /**
     * @var string required concrete5 version
     */
    protected $appVersionRequired = '9.0.0';

    /**
     * @var string package version
     */
    protected $pkgVersion = '0.0.1';

    /**
     * {@inheritdoc}
     */
    protected $pkgAutoloaderRegistries = [
        'src' => '\Macareux\JobRunner',
    ];

    /**
     * Returns the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t('The task to run legacy automated jobs.');
    }

    /**
     * Returns the installed package name.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t('Macareux Job Runner');
    }

    public function install()
    {
        $package = parent::install();

        $this->installContentFile('install/tasks.xml');

        return $package;
    }

    public function on_start()
    {
        /** @var Manager $manager */
        $manager = $this->app->make(Manager::class);
        $manager->extend('job', function () {
            return new JobController();
        });

        if (Application::isRunThroughCommandLineInterface()) {
            /** @var ConsoleApplication $console */
            $console = $this->app->make('console');
            $console->add(new ConvertQueueableJobCommand());
        }
    }
}
