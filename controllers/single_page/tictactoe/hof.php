<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage\Tictactoe;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Package\Ht7C5Base\Traits\CanHandleFlashBag;
use Concrete\Package\Ht7C5WsChatSimple\Controller\SinglePage\AbstractChatPage;
use Concrete\Package\Ht7C5WsChatSimple\Entity\Ht7WsChannel;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe as TicTacToeEntity;
use Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use Concrete\Package\Ht7C5WsTictactoe\Models\PlayerStats;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Hof extends PageController
//class Game extends AbstractChatPage
{

    use CanHandleFlashBag;
    public function view()
    {
        $this->requireAsset('ht7-ws/tictactoe');
        $this->requireAsset('ht7-widgets/body-overlay');

        $pkgH = $this->app->make('helper/ht7/package/base');
        $pkg = $pkgH->getPackage($this);
        $players = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsPlayer::class)
            ->findBy([]);

        $this->set('pkg', $pkg);
        $this->set('pkgHandle', $pkg->getPackageHandle());
        $this->set('players', $this->getSortedPlayers($players));
    }
    protected function getPlayerStats(Ht7WsPlayer $player)
    {
        $arr = [
            'draw' => 0,
            'entity' => $player,
            'lost' => 0,
            'played' => 0,
            'won' => 0,
        ];
        $u = $player->getUser();

        /* @var $game TicTacToeEntity */
        foreach ($player->getTicTacToes() as $game) {
            if ($game->getIsKi() || !$game->getIsFinished()) {
                continue;
            }
            $arr['played']++;

            if ($game->getWinnerId() === 0) {
                $arr['draw']++;
            } elseif ($game->getWinnerId() == $u->getUserID()) {
                $arr['won']++;
            } else {
                $arr['lost']++;
            }
        }

        return new PlayerStats($arr);
    }
    /**
     *
     * @param Ht7WsPlayer[] $players
     */
    protected function getSortedPlayers($players)
    {
        $sorted = [];

        foreach ($players as $player) {
            if ($player->getTicTacToes()->count() === 0) {
                continue;
            }

            $sorted[] = $this->getPlayerStats($player);
        }
        usort($sorted, function($a, $b) {
            return $a->getQuotation() == $b->getQuotation() ? 0 : (($a->getQuotation() > $b->getQuotation()) ? -1 : 1);
        });

        return $sorted;
    }
}
