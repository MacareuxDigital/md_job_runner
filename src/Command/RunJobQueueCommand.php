<?php

namespace Macareux\JobRunner\Command;

class RunJobQueueCommand extends RunJobCommand
{
    protected $body = '';

    /**
     * @param string $jobHandle
     * @param string $body
     */
    public function __construct(string $jobHandle, string $body)
    {
        parent::__construct($jobHandle);
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
