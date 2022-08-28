<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Foundation\Command\Command;

class RunJobCommand extends Command
{
    protected $jobHandle = '';

    /**
     * @param string $jobHandle
     */
    public function __construct(string $jobHandle)
    {
        $this->jobHandle = $jobHandle;
    }

    /**
     * @return string
     */
    public function getJobHandle(): string
    {
        return $this->jobHandle;
    }

    /**
     * @param string $jobHandle
     */
    public function setJobHandle(string $jobHandle): void
    {
        $this->jobHandle = $jobHandle;
    }
}
