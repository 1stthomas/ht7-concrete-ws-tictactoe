<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Game start message.
 */
class GameStart extends AbstractGameMessage
{
    /**
     * @var string              The url to the game.
     */
    protected $url;

    public function __construct(array $msg)
    {
        $msg['action'] = 'start';

        $this->url = empty($msg['url']) ? '' : $msg['url'];

        parent::__construct($msg);
    }
    /**
     * Get the redirection url to the game.
     *
     * @return  string              The url.
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'url' => $this->getUrl(),
            ] + parent::jsonSerialize();
    }
}
