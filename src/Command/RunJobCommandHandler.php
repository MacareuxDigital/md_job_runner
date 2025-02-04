<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Support\Facade\Application;
use Macareux\JobRunner\Job\Service;

class RunJobCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    public function __invoke(RunJobCommand $command)
    {
        $app = Application::getFacadeApplication();
        /** @var Service $service */
        $service = $app->make(Service::class);
        $job = $service->getJobByHandle($command->getJobHandle());
        if ($job) {
            $this->output->write('Running job: ' . $command->getJobHandle());
            $job->run();
        }
    }
}
