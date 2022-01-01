<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

class LobbyAdd extends AbstractGameMessage
{
    protected $isRoom;

    public function __construct(array $msg)
    {
        $msg['action'] = 'lobby_add';

        parent::__construct($msg);
    }
}
