<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Game move message.
 */
class GameTurn extends AbstractGameMessage
{
    /**
     * @var string              "row-index,col-index" - "0,1" for the 2nd cell
     *                          of the 1st row.
     */
    protected $move;

    /**
     * @var integer             0 if no winner, otherwise 1 or 2.
     */
    protected $winner;

    public function __construct(array $msg)
    {
        $msg['action'] = 'turn';

        $this->move = empty($msg['move']) ? '' : $msg['move'];
        $this->winner = empty($msg['winner']) ? 0 : $msg['winner'];

        parent::__construct($msg);
    }
    /**
     *
     * @return string
     */
    public function getMove()
    {
        return $this->move;
    }
    /**
     * Get the player number who has won.
     *
     * @return integer              1 or 2 if a player has won, otherwise 0.
     */
    public function getWinner()
    {
        return $this->winner;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'move' => $this->getMove(),
//            'who' => $this->getWho(),
            'winner' => $this->getWinner(),
            ] + parent::jsonSerialize();
    }
}
