<?php

namespace Macareux\JobRunner\Queue;

class Queue
{
    protected $messages = [];

    public function send($mixed)
    {
        $this->messages[] = (string) $mixed;
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
