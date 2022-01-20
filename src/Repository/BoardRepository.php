<?php

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Board|null find($id, $lockMode = null, $lockVersion = null)
 * @method Board|null findOneBy(array $criteria, array $orderBy = null)
 * @method Board[]    findAll()
 * @method Board[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

    public function findDifferentSuitableBoards(Board $board)
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select('b')
            ->where('b.id != :id')
            ->setParameter('id', $board->getId()->toBinary())
            ->andWhere('b.municipality = :municipality')
            ->setParameter('municipality', $board->getMunicipality()->getId()->toBinary())
            ->andWhere('b.caseFormType = :caseFormType')
            ->setParameter('caseFormType', $board->getCaseFormType())
        ;

        return $qb->getQuery()->getResult();
    }
}
