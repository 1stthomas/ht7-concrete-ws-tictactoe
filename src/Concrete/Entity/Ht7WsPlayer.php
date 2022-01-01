<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Entity;

use Concrete\Core\Entity\User\User;
use Concrete\Package\Ht7C5Base\Entity\OrmEntityExtended;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Ht7WsPlayers")
 */
class Ht7WsPlayer extends OrmEntityExtended implements \JsonSerializable
{
    /**
     * Make sure deleted entities are really removed from DB.
     *
     * @var     boolean                 false if the entity has to be removed
     *                                  from DB.
     */
    protected static $safeDelete = false;

    /**
     * @var     string
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $avatar;

    /**
     * @var     string
     *
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    protected $avatarColor;

    /**
     * One player has one user
     *
     * @var Concrete\Core\Entity\User\User
     *
     * @ORM\OneToOne(targetEntity="\Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="uID")
     */
    protected $user;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="TicTacToe", inversedBy="players")
     * @JoinTable(name="Ht7WsPlayersTicTacToes")
     */
    protected $ticTacToes;

    public function __construct(array $data = [])
    {
        $this->ticTacToes = new ArrayCollection();

        parent::__construct($data);
    }
    public function addTicTacToe(TicTacToe $ticTacToe)
    {
        if ($this->ticTacToes->contains($ticTacToe)) {
            return;
        }

        $this->ticTacToes->add($ticTacToe);

        $ticTacToe->addHt7WsPlayer($this);

        return $this;
    }
    /**
     * @return  string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    /**
     * @return  string
     */
    public function getAvatarColor()
    {
        return $this->avatarColor;
    }
    /**
     *
     * @return Collection
     */
    public function getTicTacToes()
    {
        return $this->ticTacToes;
    }
    /**
     * @return  \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }
    public function jsonSerialize()
    {
        return [
            'avatar' => $this->getAvatar(),
            'avatarColor' => $this->getAvatarColor(),
//            'ticTacToes' => $this->getTicTacToes()->filter(function($ttt) {
//                    return $ttt->getHash();
//                }),
            'userId' => $this->getUser()->getUserID(),
            'userPw' => $this->getUser()->getUserPassword(),
        ];
    }
    public function removeTicTacToe(TicTacToe $ticTacToe)
    {
        if (!$this->ticTacToes->contains($ticTacToe)) {
            return;
        }

        $this->ticTacToes->removeElement($ticTacToe);

        $ticTacToe->removeHt7WsPlayer($this);
    }
    /**
     * @param   string
     * @return  Player
     */
    public function setAvatar(string $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }
    /**
     * @param   string
     * @return  Player
     */
    public function setAvatarColor(string $avatarColor)
    {
        $this->avatarColor = $avatarColor;

        return $this;
    }
    /**
     * @param   \Concrete\Core\Entity\User\User
     * @return  Player
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
