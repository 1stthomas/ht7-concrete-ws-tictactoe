<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Repository;

use \Doctrine\ORM\EntityRepository;
use \Concrete\Package\Ht7C5WsTictactoe\Entity\Ht7WsPlayer;

class TicTacToeRepository extends EntityRepository
{
    public function findAllUnfinishedByUserId(int $uId)
    {
        $qb = $this->createQueryBuilder('ttt');

        $result = $qb->select('t')
            ->leftJoin(Ht7WsPlayer::class, 'pl')
            ->where('isFinished', ':isFinished')
            ->andWhere('pl.user_id', ':user_id')
            ->setParameter('user_id', $uId)
            ->setParameter('isFinished', 1)
            ->getQuery()
            ->getResult();

//        return $this->findBy([
//                    'uId' => $uId,
//                    'isFinished' => false,
//                    'deletedAt' => null
//        ]);
    }
    public function findAllOpenByUserId(int $uId)
    {
        return $this->findBy([
                'uId' => $uId,
                'deletedAt' => null
        ]);
    }
    public function getAllOpenAsValueObjects()
    {
        $vos = [];

        foreach ($this->findAllOpen() as $game) {

        }
    }
}
