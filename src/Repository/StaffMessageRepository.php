<?php

namespace App\Repository;

use App\Entity\StaffMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Parameter;

/**
 * @method StaffMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method StaffMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method StaffMessage[]    findAll()
 * @method StaffMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StaffMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StaffMessage::class);
    }

    public function findAllBy($filter, $order)
    {
        $qb = $this->createQueryBuilder('s')
            ->select('s');

            if($filter == 'user'){
                $qb->leftJoin('s.user', 'u')
                ->orderBy('u.username', $order);
            } else{
                $qb->orderBy('s.'.$filter, $order);
                // ->setParameter('filter', $filter)
                // ->setParameter('order', $order);
            }

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return StaffMessage[] Returns an array of StaffMessage objects
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

    /*
    public function findOneBySomeField($value): ?StaffMessage
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
