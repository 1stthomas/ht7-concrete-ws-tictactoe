<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage\Tictactoe;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Package\Ht7C5Base\Traits\CanHandleFlashBag;
use Concrete\Package\Ht7C5WsChatSimple\Controller\SinglePage\AbstractChatPage;
use Concrete\Package\Ht7C5WsChatSimple\Entity\Ht7WsChannel;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe as TicTacToeEntity;
use Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Game extends PageController
//class Game extends AbstractChatPage
{

    use CanHandleFlashBag;
    /**
     * Launche the game with the present hash.
     *
     * @param   string  $hash               The hash of the game to launch.
     * @return  Concrete\Core\Http\Response
     */
    public function launch(string $hash = '')
    {
        /* @var $game TicTacToeEntity */
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToeEntity::class)
            ->findOneBy(['hash' => $hash]);

        $game->setIsRunning(true);

        $game->save();

        $responseFactory = $this->app->make(ResponseFactory::class);

        return $responseFactory->redirect($this->action('play', $game->getHash()));
    }
    public function leave(string $hash = '')
    {
        /* @var $game User */
        $u = Application::getFacadeApplication()->make(User::class);
        /* @var $game TicTacToeEntity */
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToeEntity::class)
            ->findOneBy(['hash' => $hash]);
        /* @var $player Ht7WsPlayer */
        $player = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsPlayer::class)
            ->findOneBy(['user' => $u->getUserInfoObject()->getEntityObject()]);

        if (is_object($player) && $game->getHt7WsPlayers()->contains($player) &&
            (int) $game->getAdminId() !== (int) $u->getUserID()) {
            $player->removeTicTacToe($game);
            $em = Application::getFacadeApplication()
                ->make(EntityManagerInterface::class);

            $em->persist($game);
            $em->persist($player);
            $em->flush();
        }

        $responseFactory = $this->app->make(ResponseFactory::class);

        return $responseFactory->redirect(Url::to('/tictactoe/lobby'));
    }
    public function play(string $hash)
    {
        /* @var $game TicTacToeEntity */
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToeEntity::class)
            ->findOneBy(['hash' => $hash]);

        if (is_object($game)) {
            if ($game->getIsFinished()) {
                $this->setErrorMessage(tc(
                        'ht7_c5_ws_tictactoe',
                        'The requested game has already been finished.'
                ));
                $responseFactory = $this->app->make(ResponseFactory::class);

                return $responseFactory->redirect(Url::to('/tictactoe/lobby'));
            } else {
                $u = Application::getFacadeApplication()->make(User::class);

                $this->requireAsset('core/app');
                $this->requireAsset('ht7-widgets/body-overlay');
                $this->requireAsset('ht7-widgets/simple');
                $this->view();

                $channel = $this->app->make(EntityManagerInterface::class)
                    ->getRepository(Ht7WsChannel::class)
                    ->findOneBy(['name' => 'Duo']);

                $cH = $this->app->make('helper/ht7/ws/chat');
                $cH->setController($this);
                $cH->setChannel($channel);
                $cH->setChannelId($game->getHash());
                $wsH = $this->app->make('ht7/ws');

                $this->set('appId', (new Provider())->getApplicationByHandle('tictactoe')->getIdentifier());
                $this->set('cH', $cH);
                $this->set('game', $game);
                $this->set('hasChat', true);
                $this->set('isAdmin', ($u->getUserID() == $game->getAdminId()));
                $this->set('user', $u);
                $this->set('wsUrl', $wsH->getWsUrl());

                $this->render('tictactoe/game');
            }
        } else {
            $this->setErrorMessage(tc('ht7_c5_ws_tictactoe', 'The requested game does not exist.'));
            $responseFactory = $this->app->make(ResponseFactory::class);

            return $responseFactory->redirect(Url::to('/tictactoe/lobby'));
        }
    }
    public function restore()
    {

    }
    public function view()
    {
        $this->requireAsset('ht7-ws/tictactoe');

        $pkgH = $this->app->make('helper/ht7/package/base');
        $pkg = $pkgH->getPackage($this);

        $this->set('pkg', $pkg);
        $this->set('pkgHandle', $pkg->getPackageHandle());
        $this->set('pkgHandleBase', $pkgH->getPackage($pkgH));
    }
    public function watch(string $hash)
    {
        $u = $this->app->make(User::class);

        /* @var $game TicTacToeEntity */
        $game = Application::getFacadeApplication()
            ->make(EntityManagerInterface::class)
            ->getRepository(TicTacToeEntity::class)
            ->findOneBy(['hash' => $hash]);

        if (!is_object($game)) {
            $responseFactory = $this->app->make(ResponseFactory::class);

            return $responseFactory->redirect(Url::to('/tictactoe/mygames'));
        }

        $winner = Application::getFacadeApplication()
            ->make(UserInfoRepository::class)
            ->getByID(['uID' => $game->getWinnerId()]);

        $this->view();

        $this->set('game', $game);
        $this->set('u', $u);
        $this->set('ui', $u->getUserInfoObject());
        $this->set('youWon', $game->getWinnerId() == $u->getUserID());
        $this->set('winner', $winner);

        $this->render('tictactoe/watch');
    }
}
