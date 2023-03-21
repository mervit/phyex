<?php

namespace App\Repository;

use App\Entity\ExerciseTypeParam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExerciseTypeParam>
 *
 * @method ExerciseTypeParam|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExerciseTypeParam|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExerciseTypeParam[]    findAll()
 * @method ExerciseTypeParam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseTypeParamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseTypeParam::class);
    }

    public function save(ExerciseTypeParam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExerciseTypeParam $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ExerciseTypeParam[] Returns an array of ExerciseTypeParam objects
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

//    public function findOneBySomeField($value): ?ExerciseTypeParam
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
