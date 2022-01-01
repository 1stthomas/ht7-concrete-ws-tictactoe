<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Message after a successful connection.
 *
 * With such a message all existing authorized clients are informed about the
 * new client.
 */
class Hello extends AbstractGameMessage
{
    public function __construct(array $msg = [])
    {
        $msg['action'] = 'hello';

        parent::__construct($msg);
    }
}
