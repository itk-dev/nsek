<?php

namespace App\Repository;

use App\Entity\RentBoardCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RentBoardCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method RentBoardCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method RentBoardCase[]    findAll()
 * @method RentBoardCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RentBoardCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentBoardCase::class);
    }
}
