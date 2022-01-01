<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Chat message.
 *
 * The action of this message is changed from 'tweet' to 'response' to show
 * the other clients that this is message from another client (could also be done
 * by analyzing the message).
 */
class GameFinish extends AbstractGameMessage
{
    protected $winner;

    public function __construct(array $msg)
    {
        $this->action = 'finish';
        $this->winner = $msg['winner'];

        parent::__construct($msg);
    }
    public function getWinner()
    {
        return $this->winner;
    }
    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();

        $arr['winner'] = $this->move;

        return $arr;
    }
}
