<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsServer\Messages\AbstractServerMessage;

abstract class AbstractGameMessage extends AbstractServerMessage
{
    /**
     * The id of the related game.
     *
     * @var string
     */
//    protected $gameId;
    /**
     * The hash of the related game.
     *
     * @var string
     */
    protected $hash;
//    protected $icon;

    /**
     * The player number.
     *
     * @var integer                     1 or 2.
     */
    protected $player;

    public function __construct(array $msg)
    {
        $this->hash = empty($msg['hash']) ? '' : $msg['hash'];
        $this->player = empty($msg['player']) ? '' : $msg['player'];

        parent::__construct($msg);
    }
    /**
     * Get the id of he present game.
     *
     * @return string
     */
//    public function getGameId()
//    {
//        return $this->gameId;
//    }
    /**
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    /**
     * Get the icon of the player in the present game.
     *
     * @return type
     */
//    public function getIcon()
//    {
//        return $this->icon;
//    }
    /**
     *
     * @return integer
     */
    public function getPlayer()
    {
        return (int) $this->player;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'hash' => $this->getHash(),
            'player' => $this->getPlayer(),
            ] + parent::jsonSerialize();
    }
}
