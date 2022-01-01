<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage\Tictactoe;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Package\Ht7C5Base\Traits\CanHandleFlashBag;
use Concrete\Package\Ht7C5WsChatSimple\Entity\Ht7WsChannel;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Lobby extends PageController
{

    use CanHandleFlashBag;
    protected $wsUrl;

    public function abort(string $hash)
    {
        /* @var $game TicTacToe */
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToe::class)
            ->findOneBy(['hash' => $hash]);

        if (empty($game->getMoves())) {
            $game->delete();
        }

        $responseFactory = $this->app->make(ResponseFactory::class);

        return $responseFactory->redirect($this->action(''));
    }
    public function getWsUrl()
    {
        if (empty($this->wsUrl)) {
            $pkgH = $this->app->make('helper/ht7/package/base');
            $provider = new Provider();
            $configWs = $pkgH->getPackageFileConfig($provider)
                ->get('ws_server');

            $this->wsUrl = str_replace(['http', 'https'], 'ws', $configWs['route'])
                . ':' . $configWs['port'];
        }

        return $this->wsUrl;
    }
    /**
     * Start a game vs another player.
     *
     * This method will create a new game entity and show the room for a game
     * vs another player.
     */
    public function human()
    {
        $u = Application::getFacadeApplication()->make(User::class);
        $player = $this->getOrCreatePlayer($u);

        $game = new TicTacToe([
            'adminId' => $u->getUserID(),
            'hash' => bin2hex(random_bytes(32)),
        ]);
        $game->addHt7WsPlayer($player);

        $game->save();
//
//        $responseFactory = $this->app->make(ResponseFactory::class);
//        return $responseFactory->redirect($this->action('room', $game->getHash()));

        $this->app->make(ResponseFactoryInterface::class)
            ->redirect($this->action('room', $game->getHash()))
            ->send();
        exit;
    }
    /**
     * Start a new game vs ai.
     */
    public function ki()
    {
        $u = Application::getFacadeApplication()->make(User::class);
        $player = $this->getOrCreatePlayer($u);

        $game = new TicTacToe([
            'adminId' => $u->getUserID(),
            'hash' => bin2hex(random_bytes(32)),
            'isKi' => true,
        ]);
        $game->addHt7WsPlayer($player);

        $game->save();

        $responseFactory = $this->app->make(ResponseFactory::class);
        $url = '/tictactoe/game/launch/' . $game->getHash();

        return $responseFactory->redirect($url);
    }
    public function room(string $hash)
    {
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToe::class)
            ->findOneBy(['hash' => $hash]);
        $u = Application::getFacadeApplication()->make(User::class);

        if (!is_object($game)) {
            $responseFactory = $this->app->make(ResponseFactory::class);
            $url = '/tictactoe/lobby';

            return $responseFactory->redirect($url);
        } elseif (!$this->handleParticipation($game, $u)) {
            // redirect
            die('Access denied.');
        }

        $this->requireAsset('ht7-ws/tictactoe');

        $pkgH = $this->app->make('helper/ht7/package/base');
        $channel = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsChannel::class)
            ->findOneBy(['name' => 'Duo']);

        $cH = $this->app->make('helper/ht7/ws/chat');
        $cH->setController($this);
        $cH->setChannel($channel);
        $cH->setChannelId($hash);

        $this->set('appId', (new Provider())->getApplicationByHandle('tictactoe')->getIdentifier());
        $this->set('cH', $cH);
        $this->set('fb', $this->getFlashBag());
        $this->set('hasChat', true);
        $this->set('pkgHandle', $pkgH->getPackageHandle($this));
        $this->set('game', $game);
        $this->set('isAdmin', ($u->getUserID() == $game->getAdminId()));
        $this->set('u', $u);
        $this->set('ui', $u->getUserInfoObject());
        $this->set('wsUrl', $this->getWsUrl());

        $this->render('tictactoe/room');
    }
    public function validate()
    {

    }
    public function view($task = null)
    {
        $this->requireAsset('ht7-ws/tictactoe');

        $u = $this->app->make(User::class);
        $pkgH = $this->app->make('helper/ht7/package/base');
        $pkgHandle = $pkgH->getPackageHandle($this);

        if ($task !== null) {
            if ($task === 'deleted') {
                $this->setErrorMessage(tc($pkgHandle, 'The game has been closed.'));
            }
        }

        $channel = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsChannel::class)
            ->findOneBy(['name' => 'Lobby']);

        $cH = $this->app->make('helper/ht7/ws/chat');
        $cH->setController($this);
        $cH->setChannel($channel);

        $games = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToe::class)
            ->findBy(['isFinished' => false, 'isRunning' => false, 'isKi' => false]);

        $this->set('appId', (new Provider())->getApplicationByHandle('tictactoe')->getIdentifier());
        $this->set('cH', $cH);
        $this->set('fb', $this->getFlashBag());
        $this->set('hasChat', true);
        $this->set('games', $games);
        $this->set('pkgHandle', $pkgHandle);
        $this->set('u', $u);
        $this->set('ui', $u->getUserInfoObject());
        $this->set('wsUrl', $this->getWsUrl());
    }
    protected function getOrCreatePlayer(User $u)
    {
        $player = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsPlayer::class)
            ->findOneBy(['user' => $u->getUserInfoObject()->getEntityObject()]);

        if (empty($player)) {
            $player = new Ht7WsPlayer([
                'user' => $u->getUserInfoObject()->getEntityObject(),
            ]);

            $player->save();
        }

        return $player;
    }
    protected function handleParticipation(TicTacToe $game, User $u)
    {
        $return = false;
        $player = $this->getOrCreatePlayer($u);

        if ($game->getHt7WsPlayers()->contains($player)) {
            $return = true;
        } elseif (count($game->getHt7WsPlayers()) === 1) {
            if ((int) $u->getUserID() !== (int) $game->getAdminId()) {
                $game->addHt7WsPlayer($player);

                $game->save();
            }
            $return = true;
        }

        return $return;
    }
}
