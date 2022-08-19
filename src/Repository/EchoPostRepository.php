<?php

namespace App\Repository;

use App\Entity\EchoPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EchoPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method EchoPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method EchoPost[]    findAll()
 * @method EchoPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchoPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EchoPost::class);
    }

    public function findList($parameters =[])
    {

        $qb = $this->createQueryBuilder("e")
            ->select('e')
        ;
        
        if ($parameters['filter'] == 'vote') {
            $qb->leftJoin('e.votes', 'v')
            ->orderBy('COUNT(v)', $parameters['order'])
            ->groupBy('e');
        } elseif ($parameters['filter'] == 'user') {
            $qb->leftJoin('e.'.$parameters['filter'], 'u')
            ->orderBy('u.username', $parameters['order']);
        } else {
            $qb->orderBy('e.' . $parameters['filter'], $parameters['order']);
        }

        if (isset($parameters['moderate'])) {
            $qb->andWhere(':moderate = e.isModerated')
                ->setParameter(':moderate', $parameters["moderate"]);
        }

        if (isset($parameters['tag'])) {
            $qb->andWhere(':tag MEMBER OF e.tags')
                ->setParameter(':tag', $parameters["tag"]);
        }

        if (isset($parameters['user'])) {
            $qb->andWhere(':user = e.user')
                ->setParameter(':user', $parameters["user"]);
        }

        if (isset($parameters['search'])) {
            $qb->andWhere('e.title LIKE :search')
                ->orWhere('e.content LIKE :search')
                ->orWhere('e.id LIKE :search')
                ->leftJoin('e.user', 'u')
                ->orWhere('u.username LIKE :search')
                ->setParameter(':search', '%'.$parameters["search"].'%');
        }


        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return EchoPost[] Returns an array of EchoPost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EchoPost
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
