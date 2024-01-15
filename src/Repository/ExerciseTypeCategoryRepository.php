<?php

namespace App\Repository;

use App\Entity\ExerciseTypeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciseTypeCategory>
 *
 * @method ExerciseTypeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExerciseTypeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExerciseTypeCategory[]    findAll()
 * @method ExerciseTypeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseTypeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseTypeCategory::class);
    }

    public function save(ExerciseTypeCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExerciseTypeCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria($criteria = null, $orderBy = null, $limit = null, $offset = null){

        $qb = $this->createQueryBuilder('c');

        if($criteria){
            $qb->addCriteria($criteria);
        }

        if($orderBy){
            foreach ($orderBy as $obk => $obv){
                $qb->addOrderBy($obk, $obv);
            }
        }

        if($limit) {
            $qb->setMaxResults($limit);
        }

        if($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();

    }

    public function countByCriteria($criteria){

        return $this->createQueryBuilder('c')
            ->addCriteria($criteria)
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

//    /**
//     * @return ExerciseTypeCategory[] Returns an array of ExerciseTypeCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExerciseTypeCategory
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
