<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Models;

class PlayerStats
{
    /**
     * @var int             The count of games ending in a draw.
     */
    protected $draw;

    /**
     * @var int             The related player entity.
     */
    protected $entity;

    /**
     * @var int             The count of games lost.
     */
    protected $lost;

    /**
     * @var int             The count of games played.
     */
    protected $played;

    /**
     * @var float           The quotation of <code>$won/$played</code>
     */
    protected $quotation;

    /**
     * @var int             The count of games won.
     */
    protected $won;

    public function __construct(array $values)
    {
        $this->draw = empty($values['draw']) ? 0 : (int) $values['draw'];
        $this->entity = empty($values['entity']) ? null : $values['entity'];
        $this->lost = empty($values['lost']) ? 0 : (int) $values['lost'];
        $this->played = empty($values['played']) ? 0 : (int) $values['played'];
        $this->won = empty($values['won']) ? 0 : (int) $values['won'];
        $this->quotation = empty($values['won']) || empty($values['played']) ?
            0 : (int) round((float) ($values['won'] / $values['played']) * 100);
    }
    /**
     * Get the count of games ending in a draw.
     *
     * @return int
     */
    public function getDraw()
    {
        return $this->draw;
    }
    /**
     * Get the player entity.
     *
     * @return \Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer
     */
    public function getEntity()
    {
        return $this->entity;
    }
    /**
     * Get the count of games lost.
     *
     * @return int
     */
    public function getLost()
    {
        return $this->lost;
    }
    /**
     * Get the count of games played.
     *
     * @return int
     */
    public function getPlayed()
    {
        return $this->played;
    }
    /**
     * @return float
     */
    public function getQuotation()
    {
        return $this->quotation;
    }
    /**
     * Get the count of games won.
     *
     * @return int
     */
    public function getWon()
    {
        return $this->won;
    }
}
