<?php

namespace App\Repository;

use App\Entity\TmdbGenres;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TmdbGenres|null find($id, $lockMode = null, $lockVersion = null)
 * @method TmdbGenres|null findOneBy(array $criteria, array $orderBy = null)
 * @method TmdbGenres[]    findAll()
 * @method TmdbGenres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TmdbGenresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TmdbGenres::class);
    }

    public function getLastSync()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT last_sync FROM tmdb_genres LIMIT 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    
        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchColumn();
    }

    public function getGenresIdAndName()
    {
        $query = $this->createQueryBuilder('g')->select('g.tmdbId, g.name');
        return array_column($query->getQuery()->getResult(), 'name', 'tmdbId');
    }

    // /**
    //  * @return TmdbGenres[] Returns an array of TmdbGenres objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TmdbGenres
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
