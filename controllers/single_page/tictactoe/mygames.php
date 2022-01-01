<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Controller\SinglePage\Tictactoe;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Package\Ht7C5Base\Traits\CanHandleFlashBag;
use Concrete\Package\Ht7C5WsTictactoe\Entity\TicTacToe as TicTacToeEntity;
use Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;
use Concrete\Package\Ht7C5WsServer\Application\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

defined('C5_EXECUTE') or die('Access Denied.');

class Mygames extends PageController
{

    use CanHandleFlashBag;
    /**
     * Show the List of the games played
     */
    public function view()
    {
        $this->requireAsset('core/app');
        $this->requireAsset('ht7-widgets/body-overlay');
        $this->requireAsset('ht7-ws/tictactoe');

        $pkgH = $this->app->make('helper/ht7/package/base');
        $pkg = $pkgH->getPackage($this);

        $expensiveCache = \Core::make('cache/expensive');
        $expensiveCache->disableAll();
        \Log::addEntry(print_r(get_class_methods($expensiveCache), true));

        $u = $this->app->make(User::class);
        \Log::addEntry('view - uid: ' . $u->getUserID());
        $uE = $this->app->make(UserInfoRepository::class)->getByID($u->getUserID());
        \Log::addEntry('view - uid: ' . $uE->getUserID());
        $player = $this->app->make(EntityManagerInterface::class)
            ->getRepository(Ht7WsPlayer::class)
            ->findOneBy(['user' => $uE]);
//        $rsm = new ResultSetMapping();
//        $query = $this->app->make(EntityManagerInterface::class)
//            ->createNativeQuery('SELECT id FROM Ht7WsPlayers WHERE user_id = :uid', $rsm);
//        $query->setParameter('uid', $u->getUserID());
//        $res = $query->getResult();
//        \Log::addEntry(print_r($res, true));
//        if (!is_object($player) || $player->getUser()->getUserID() != $u->getUserID()) {
//            \Log::addEntry('view - uid - neu: ' . $u->getUserID());
////            $repo = $this->app->make(EntityManagerInterface::class)
////                ->getRepository(Ht7WsPlayer::class);
//            $rsm = new ResultSetMapping();
//            $query = $this->app->make(EntityManagerInterface::class)
//                ->createNativeQuery('SELECT id FROM Ht7WsPlayers WHERE user_id = :uid', $rsm);
//            $query->setParameter('uid', $u->getUserID());
////            $qb = $this->app->make(EntityManagerInterface::class)
////                ->createQueryBuilder();
////            $qb->select('pl.id')
////                ->from('Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer', 'pl')
////                ->where('pl.user_id = :uid')
////                ->setParameter('uid', $u->getUserID());
////            $query = $qb->getQuery();
//
//            \Log::addEntry(print_r($query->getResult(), true));
////                ->findOneBy(['user_id' => $u->getUserID()]);
//        }
//        if (is_object($player)) {
//            \Log::addEntry('view - obj - ' . $player->getId() . ' - ' . $player->getUser()->getUserID());
//        } else {
//            \Log::addEntry('view - KEIN obj');
//        }

        $this->set('pkg', $pkg);
        $this->set('pkgHandle', $pkg->getPackageHandle());
        $this->set('player', $player);
    }
}
