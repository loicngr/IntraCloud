<?php

namespace App\Repository;

use App\Entity\Server;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Server|null find($id, $lockMode = null, $lockVersion = null)
 * @method Server|null findOneBy(array $criteria, array $orderBy = null)
 * @method Server[]    findAll()
 * @method Server[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Server::class);
    }

    // /**
    //  * @return Server[] Returns an array of Server objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByAdressePortLoginName($adresse, $port, $username, $name)
    {
        try {
            return $this->createQueryBuilder('s')
                ->andWhere('s.adresse = :adresse AND s.port = :port AND s.username = :username AND s.name = :name')
                ->setParameter('adresse', $adresse)
                ->setParameter('port', $port)
                ->setParameter('username', $username)
                ->setParameter('name', $name)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findByAdressePortLogin($adresse, $port, $username)
    {
        try {
            return $this->createQueryBuilder('s')
                ->andWhere('s.adresse = :adresse AND s.port = :port AND s.username = :username')
                ->setParameter('adresse', $adresse)
                ->setParameter('port', $port)
                ->setParameter('username', $username)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findByAdresseAndPort($adresse, $port)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.adresse = :adresse AND s.port = :port')
            ->setParameter('adresse', $adresse)
            ->setParameter('port', $port)
            ->getQuery()
            ->getResult();
    }
}
