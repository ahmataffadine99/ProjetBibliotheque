<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }


    public function findByKeyword(string $keyword): array
    {
        $queryBuilder = $this->createQueryBuilder('l')
            ->leftJoin('l.auteurs', 'a')
            ->leftJoin('l.genres', 'g')
            ->where('l.Titre LIKE :keyword')
            ->orWhere('l.Description LIKE :keyword')
            ->orWhere('a.nom LIKE :keyword')
            ->orWhere('a.prenom LIKE :keyword')
            ->orWhere('g.nom LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->orderBy('l.Titre', 'ASC');

        return $queryBuilder->getQuery()->getResult();
    }

    //    /**
    //     * @return Livre[] Returns an array of Livre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livre
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


}
