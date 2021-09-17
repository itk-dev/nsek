<?php

namespace App\Repository;

use App\Entity\CaseEntity;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function getNotesQueryByCase(CaseEntity $case): Query
    {
        return $this->createQueryBuilder('n')
            ->where('n.caseEntity = :caseObject')
            ->setParameter('caseObject', $case->getId()->toBinary())
            ->orderBy('n.updatedAt', 'DESC')
            ->getQuery();
    }

    /**
     * @return Note[]
     */
    public function findMostRecentNotesByCase(CaseEntity $case, int $numberOfNotes): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.caseEntity = :caseObject')
            ->setParameter('caseObject', $case->getId()->toBinary())
            ->orderBy('n.updatedAt', 'DESC')
            ->setMaxResults($numberOfNotes)
            ->getQuery()
            ->getResult();
    }
}
