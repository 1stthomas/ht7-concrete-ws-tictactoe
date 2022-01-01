<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Message when a user passed the timelimit.
 */
class GameLost extends AbstractGameMessage
{
    protected $cause;

    public function __construct(array $msg = [])
    {
        $msg['action'] = 'lost';

        $this->cause = empty($msg['cause']) ? '' : $msg['cause'];

        parent::__construct($msg);
    }
    public function getCause()
    {
        return $this->cause;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'cause' => $this->getCause(),
            ] + parent::jsonSerialize();
    }
}
