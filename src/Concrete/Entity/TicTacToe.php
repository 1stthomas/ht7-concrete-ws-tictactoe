<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Entity;

use \Concrete\Package\Ht7C5Base\Entity\OrmEntityExtended;
use \Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use \Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\Common\Collections\Collection;
use \Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Concrete\Package\Ht7C5WsTictactoe\Repository\TicTacToeRepository")
 * @ORM\Table(name="Ht7WsTicTacToes")
 */
class TicTacToe extends OrmEntityExtended implements \JsonSerializable
{
    /**
     * Make sure deleted entities are really removed from DB.
     *
     * @var     boolean                 false if the entity has to be removed
     *                                  from DB.
     */
    protected static $safeDelete = false;

    /**
     * @var int
     *
     * @ORM\Column(
     *     type="integer",
     *     nullable=false,
     *     options={"unsigned"=true}
     * )
     */
    protected $adminId;

    /**
     * @var     string
     *
     * @ORM\Column(type="text", nullable=false, length=255)
     */
    protected $hash;

    /**
     * @var     integer
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $timeLimit;

    /**
     * @var     bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isFinished = false;

    /**
     * @var     bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isRunning = false;

    /**
     * @var     bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isKi = false;

    /**
     * @var int
     *
     * @ORM\Column(
     *     type="integer",
     *     nullable=true,
     *     options={"unsigned"=true}
     * )
     */
    protected $winnerId;

    /**
     * @var     string
     *
     * @ORM\Column(type="text", nullable=false, length=255)
     */
    protected $moves;

    /**
     * @var     string
     *
     * @ORM\Column(type="text", nullable=false, length=255, options={"default" : ""})
     */
    protected $fields;

    /**
     * Many games have many players (2 ;))
     *
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Ht7WsPlayer", mappedBy="ticTacToes")
     */
    protected $players;

    public function __construct(array $data = [])
    {
        $this->players = new ArrayCollection();
        $this->moves = '';
        $this->fields = '';
        $this->timeLimit = 0;

        parent::__construct($data);
    }
    /**
     * @return  Collection                  All players.
     */
    public function addHt7WsPlayer(Ht7WsPlayer $player)
    {
        if ($this->players->contains($player)) {
            return;
        }

        $this->players->add($player);

        $player->addTicTacToe($this);

        return $this;
    }
    public function addMove(string $move)
    {
        $arr = explode(',', $this->moves);
        $arr[] = $move;
        $this->moves = implode(',', $arr);

        return $this;
    }
    public function getAdminId()
    {
        return $this->adminId;
    }
    /**
     *
     * @return  array
     */
    public function getFields()
    {
        return unserialize($this->fields);
    }
    /**
     * @return  string                  The application handle.
     */
    public function getHandle()
    {
        return $this->handle;
    }
    /**
     * @return  string
     */
    public function getHash()
    {
        return $this->hash;
    }
    /**
     * @return  Collection                  All players.
     */
    public function getHt7WsPlayers()
    {
        return $this->players;
    }
    /**
     * @return  bool
     */
    public function getIsFinished()
    {
        return $this->isFinished;
    }
    /**
     * @return  bool
     */
    public function getIsKi()
    {
        return $this->isKi;
    }
    /**
     * @return  bool
     */
    public function getIsRunning()
    {
        return $this->isRunning;
    }
    /**
     * @return  string
     */
    public function getMoves()
    {
        return $this->moves;
    }
    /**
     *
     * @return array
     */
    public function getMovesArray()
    {
        return explode(',', $this->getMoves());
    }
    /**
     * @return  integer
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }
    /**
     * @return  int                     user id
     */
    public function getWinnerId()
    {
        return $this->winnerId;
    }
    public function jsonSerialize()
    {
        return [
            'adminId' => $this->getAdminId(),
            'fields' => $this->getFields(),
            'hash' => $this->getHash(),
            'isFinished' => $this->getIsFinished(),
            'isKi' => $this->getIsKi(),
            'isRunning' => $this->getIsRunning(),
            'handle' => $this->getHandle(),
            'moves' => $this->getMovesArray(),
            'players' => $this->getHt7WsPlayers(), // todo... (?) json_encode needed?
            'timeLimit' => $this->getTimeLimit(),
            'winnerId' => $this->getWinnerId(),
        ];
    }
    public function removeHt7WsPlayer(Ht7WsPlayer $player)
    {
        if (!$this->players->contains($player)) {
            return;
        }

        $this->players->removeElement($player);

        $player->removeTicTacToe($this);
    }
    public function setAdminId(string $adminId)
    {
        $this->adminId = $adminId;
    }
    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = serialize($fields);
    }
    /**
     * @param   string
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;

        return $this;
    }
    /**
     * @param   bool
     */
    public function setIsFinished(string $isFinished)
    {
        $this->isFinished = $isFinished;

        return $this;
    }
    /**
     * @param   bool
     * @return  WsApplication
     */
    public function setIsKi(string $isKi)
    {
        $this->isKi = $isKi;

        return $this;
    }
    /**
     * @param   bool
     */
    public function setIsRunning(string $isRunning)
    {
        $this->isRunning = $isRunning;

        return $this;
    }
    /**
     * @param   array
     * @return  WsApplication
     */
    public function setMoves(array $moves)
    {
        $this->moves = $moves;

        return $this;
    }
    public function setTimeLimit(int $timeLimit)
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }
    /**
     * @param   int                     user id
     * @return  WsApplication
     */
    public function setWinnerId(int $winnerId)
    {
        $this->winnerId = $winnerId;

        return $this;
    }
}
