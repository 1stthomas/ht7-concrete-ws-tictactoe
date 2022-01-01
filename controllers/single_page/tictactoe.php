<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Concrete\Package\Ht7C5WsServer\Definitions\ApplicationStatus;
use Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Tictactoe extends PageController
{
    public function view()
//    public function view(string $gameType, int $id = null)
    {
        $this->requireAsset('ht7-ws/tictactoe');

//        $this->set('gametype', $gametype);
//        parent::view();
        $application = (new Provider)->getApplicationByHandle('tictactoe');
        $u = $this->app->make(User::class);
        $uE = $this->app->make(UserInfoRepository::class)->getByID($u->getUserID());
        $player = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsPlayer::class)
            ->findOneBy(['user' => $uE]);

        $this->set('isRunning', $application->getStatus() === ApplicationStatus::APPLICATION_ON);
        $this->set('player', $player);
    }
}
