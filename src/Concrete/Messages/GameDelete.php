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
class GameDelete extends AbstractGameMessage
{
    public function __construct(array $msg)
    {
        $this->action = 'delete';

        parent::__construct($msg);
    }
}
