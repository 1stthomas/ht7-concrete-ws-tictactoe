<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\Dialog;

use Concrete\Core\Controller\Controller;
use Concrete\Core\User\User;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe;
use Doctrine\ORM\EntityManagerInterface;

class GameFinish extends Controller
{
    protected $viewPath = 'dialogs/game_finish';

    public function view(string $id, int $winner, int $timelimit = 0)
    {
        /* @var $game User */
        $u = $this->app->make(User::class);
        /* @var $game TicTacToe */
        $game = $this->app->make(EntityManagerInterface::class)
            ->getRepository(TicTacToe::class)
            ->findOneBy(['hash' => $id]);

        $game->setIsFinished(true);
        $game->setIsRunning(false);

        if ($timelimit) {
            // A game has been lost by time limit passing.
//            $winner = $winner === 1 &&  ? ;
            $game->setWinnerId($winner);
            $winner = $winner === (int) $u->getUserID() ? 1 : 2;

            if ((int) $u->getUserID() === $game->getAdminId()) {
                $game->save();
            }
        } else {
            if ($game->getIsKi()) {
                $winner = $winner === 1 ? $u->getUserID() : -2;

                $game->setWinnerId($winner);
                $game->setFields($this->request->post('fields', []));
                $game->save();
            } else {
                if ((int) $u->getUserID() === $game->getAdminId()) {
                    $game->setWinnerId($winner);
                    $game->setFields($this->request->post('fields', []));
                    $game->save();
                }

                if ($winner > 0) {
                    $winner = $winner === (int) $u->getUserID() ? 1 : 2;
                }
            }
        }

        $this->set('isKi', $game->getIsKi());
        $this->set('winner', $winner);
    }
}
