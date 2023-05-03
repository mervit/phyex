<?php

namespace App\Repository;

use App\Entity\Exercise;
use App\Entity\ExerciseType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 *
 * @method Exercise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercise[]    findAll()
 * @method Exercise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function save(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getRandom(User $user): Exercise {

        return $this->createQueryBuilder('e')
            ->setMaxResults(1)
            ->addSelect('(SELECT COUNT(uev.id) FROM App\Entity\Evaluation AS uev WHERE uev.user = :user AND uev.exercise = e) AS HIDDEN num_users_evaluations')
            ->addSelect('(SELECT COUNT(ev.id) FROM App\Entity\Evaluation AS ev WHERE ev.exercise = e) AS HIDDEN num_evaluations')
            ->addOrderBy('num_users_evaluations', 'ASC')
            ->addOrderBy('num_evaluations', 'ASC')
            ->addOrderBy('RAND()')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findByCriteria($criteria = null, $orderBy = null, $limit = null, $offset = null){

        $qb = $this->createQueryBuilder('e');

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

        return $this->createQueryBuilder('e')
            ->addCriteria($criteria)
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

    public function countByExerciseType(ExerciseType $exerciseType) {
        return $this->createQueryBuilder('ex')
            ->select('COUNT(ex.id)')
            ->where('ex.exerciseType = :exerciseType')
            ->setParameter('exerciseType', $exerciseType)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Exercise[] Returns an array of Exercise objects
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

//    public function findOneBySomeField($value): ?Exercise
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
