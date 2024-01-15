<?php

namespace App\Repository;

use App\Entity\ExerciseType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciseType>
 *
 * @method ExerciseType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExerciseType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExerciseType[]    findAll()
 * @method ExerciseType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseType::class);
    }

    public function save(ExerciseType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExerciseType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCriteria($criteria = null, $category = null, $orderBy = null, $limit = null, $offset = null){

        $qb = $this->createQueryBuilder('et');
        $qb->addSelect('(SELECT COUNT(ex.id) FROM App\\Entity\\Exercise AS ex WHERE ex.exerciseType = et) AS exerciseCount');

        if($criteria){
            $qb->addCriteria($criteria);
        }

        if($category) {
            $qb->innerJoin('et.categories', 'c');
            $qb->andWhere('c.id = :category');
            $qb->setParameter('category', $category->getId());
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

    public function findByCategory($category){

        $qb = $this->createQueryBuilder('et');
        $qb->innerJoin('et.categories', 'c');
        $qb->andWhere('c.id = :category');
        $qb->setParameter('category', $category->getId());
        return $qb->getQuery()->getResult();

    }

    public function countByCriteria($criteria, $category){

        $qb = $this->createQueryBuilder('et');

        if($criteria){
            $qb->addCriteria($criteria);
        }

        if($category) {
            $qb->innerJoin('et.categories', 'c');
            $qb->andWhere('c.id = :category');
            $qb->setParameter('category', $category->getId());
        }

        return $qb
            ->select('COUNT(et.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

//    /**
//     * @return ExerciseType[] Returns an array of ExerciseType objects
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

//    public function findOneBySomeField($value): ?ExerciseType
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
