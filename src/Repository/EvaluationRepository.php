<?php

namespace App\Repository;

use App\Entity\Evaluation;
use App\Entity\ExerciseType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 *
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function save(Evaluation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evaluation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getNumberOfUsersEvaluations(User $user){

        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

    public function getNumberOfUsersEvaluationsForLastDays(User $user, int $days){

        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('e.created > :dt')
            ->setParameter('user', $user)
            ->setParameter('dt', new \DateTime('-' . $days . ' days'))
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

    public function getLastEvaluation(User $user){

        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->orderBy('e.created','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function getNumberOfEvaluationsInDay(User $user, \DateTime $datetime){

        $dayStart = new \DateTime($datetime->format('Y-m-d') . ' midnight');
        $dayEnd = new \DateTime($datetime->format('Y-m-d') . ' next day midnight');

        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('e.created > :ds')
            ->andWhere('e.created < :de')
            ->setParameter('user', $user)
            ->setParameter('ds', $dayStart)
            ->setParameter('de', $dayEnd)
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }


    public function findByCriteria($criteria = null, $orderBy = null, $limit = null, $offset = null){

        $qb = $this->createQueryBuilder('e');

        if($criteria){
            $qb->innerJoin('e.exercise', 'exercise');
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
            ->innerJoin('e.exercise', 'exercise')
            ->addCriteria($criteria)
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getOneOrNullResult()[1];

    }

    public function countByExerciseType(ExerciseType $exerciseType) {
        return $this->createQueryBuilder('ev')
            ->select('COUNT(ev.id)')
            ->innerJoin('ev.exercise', 'ex')
            ->where('ex.exerciseType = :exerciseType')
            ->setParameter('exerciseType', $exerciseType)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Evaluation[] Returns an array of Evaluation objects
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

//    public function findOneBySomeField($value): ?Evaluation
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
