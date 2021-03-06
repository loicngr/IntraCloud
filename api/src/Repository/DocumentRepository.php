<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    // /**
    //  * @return Document[] Returns an array of Document objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByNameAndLocationAndServer($name, $location, $server)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.name = :name AND s.location = :location AND s.server = :server')
            ->setParameter('name', $name)
            ->setParameter('location', $location)
            ->setParameter('server', $server)
            ->getQuery()
            ->getResult();
    }

    public function findOneByNameAndLocationAndServer($name, $location, $server)
    {
        try {
            return $this->createQueryBuilder('s')
                ->andWhere('s.name = :name AND s.location = :location AND s.server = :server')
                ->setParameter('name', $name)
                ->setParameter('location', $location)
                ->setParameter('server', $server)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
