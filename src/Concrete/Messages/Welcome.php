<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Message after a successful connection.
 *
 * With such a message all existing authorized clients are informed about the
 * new client.
 */
class Welcome extends AbstractGameMessage
{
    protected $isRoom;

    public function __construct(array $msg)
    {
        $msg['action'] = 'welcome';

        $this->isRoom = empty($msg['isRoom']) ? 1 : $msg['isRoom'];

        parent::__construct($msg);
    }
    public function getIsRoom()
    {
        return $this->isRoom;
    }
    public function jsonSerialize()
    {
        return [
            'isRoom' => $this->getIsRoom(),
            ] + parent::jsonSerialize();
    }
}
