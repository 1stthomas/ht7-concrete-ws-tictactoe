<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Game move message.
 */
class GameLeave extends AbstractGameMessage
{
    public function __construct(array $msg)
    {
        $msg['action'] = 'leave';

        parent::__construct($msg);
    }
}
