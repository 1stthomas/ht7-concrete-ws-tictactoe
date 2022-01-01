<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Services;

use \Concrete\Core\Application\Application;
use \Concrete\Package\Ht7C5Base\Services\AbstractService;

class Winner extends AbstractService
{
    protected $wsUrl;

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }
    /**
     * Check if the game has a winner or it has been finished by all fields occupied.
     *
     * @param   array   $game           Assoc array with game infos.
     * @param   array   $moves          Indexed 1d array with all moves.
     * @param   array   $fields         Indexed 2d array with the rows as 1st
     *                                  level and the cols as 2nd level.
     * @return  int                     -1 for a still running game, 0 for a draw.
     *                                  Otherwise the userID of the winner will
     *                                  be returned.
     */
    public function checkWinner(array $game, array $moves, array $fields)
    {
        $winner = -1;

        if ($this->checkVertical($fields, 1) ||
            $this->checkHorizontal($fields, 1) ||
            $this->checkDiagonal($fields, 1)) {

            $winner = 1;
        } else if ($this->checkVertical($fields, 2) ||
            $this->checkHorizontal($fields, 2) ||
            $this->checkDiagonal($fields, 2)) {

            $winner = 2;
        } elseif ($this->checkCount($moves)) {
            $winner = 0;
        }

        if ($winner > 0 && count($game['ps']) === 2) {
            $winner = $game['ps'][$winner]['userId'];
        }

        return $winner;
    }
    protected function checkCount(array $moves)
    {
        return count($moves) > 8;
    }
    protected function checkDiagonal(array $fields, int $player)
    {
        if ($fields[0][0] === $player && $fields[1][1] === $player && $fields[2][2] === $player) {
            return true;
        } else if ($fields[0][2] === $player && $fields[1][1] === $player && $fields[2][0] === $player) {
            return true;
        }

        return false;
    }
    protected function checkHorizontal(array $fields, int $player)
    {
        for ($i = 0; $i < 3; $i++) {
            if ($fields[$i][0] === $player && $fields[$i][1] === $player && $fields[$i][2] === $player) {
                return true;
            }
        }

        return false;
    }
    protected function checkVertical(array $fields, int $player)
    {
        for ($i = 0; $i < 3; $i++) {
            if ($fields[0][$i] === $player && $fields[1][$i] === $player && $fields[2][$i] === $player) {
                return true;
            }
        }

        return false;
    }
}
