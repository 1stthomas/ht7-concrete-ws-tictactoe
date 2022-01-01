<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Message after a successful connection.
 *
 * With such a message all existing authorized clients are informed about the
 * new client.
 */
class LobbyGameAdd extends AbstractGameMessage
{
    protected $isRoom;

    public function __construct(array $msg)
    {
        $msg['action'] = 'lobby_game_add';

        parent::__construct($msg);
    }
}
