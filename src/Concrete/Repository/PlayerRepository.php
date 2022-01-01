<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Repository;

use \Doctrine\ORM\EntityRepository;
use \Concrete\Package\Ht7C5WsServer\Entity\WsApplication;

class PlayerRepository extends EntityRepository
{
    public function findAllOpenByUserId(int $uId)
    {
//        return $this->findBy([
//                    'uId' => $uId,
//                    'deletedAt' => null
//        ]);
    }
    public function getAllOpenAsValueObjects()
    {
        $vos = [];

        foreach ($this->findAllOpen() as $game) {

        }
    }
    public function getHofList()
    {
        $players = $this->findBy([]);
        $sorted = [];
//
        foreach ($players as $player) {
            $qb = $this->createQueryBuilder('ps')
                ->select('count(ttt.ht7wsplayer_id)')
                ->innerJoin('ps.ticTacToes', 'ttt')
                ->andWhere('ps.id = :pId')
//                ->andWhere('ps.id = :pId')
//                ->andWhere('ttt.ht7wsplayer_id = :pId')
                ->setParameter('pId', $player->getId());
            $games = $player->getTicTacToes();
            $uId = $player->getUser()->getUserID();
//            $sorted[]
        }

//        $qb = $this->createQueryBuilder('ps')
//            ->innerJoin('ps.ticTacToes', 'ttt')
//            ->andWhere('cmcp.address = :address')
//            ->setParameter('course', $course);
//        return $qb->getQuery()->getResult();
    }
}
