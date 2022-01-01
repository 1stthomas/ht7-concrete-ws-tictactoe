<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Message after a successful connection.
 *
 * With such a message all existing authorized clients are informed about the
 * new client.
 */
class Error extends AbstractGameMessage
{
    /**
     * @var string
     */
    protected $text;

    public function __construct(array $msg = [])
    {
        $msg['action'] = 'error';

        $this->text = empty($msg['text']) ? '' : $msg['text'];

        parent::__construct($msg);
    }
    public function getText()
    {
        return $this->text;
    }
    public function jsonSerialize()
    {
        return [
            'text' => $this->getText(),
            ] + parent::jsonSerialize();
    }
}
