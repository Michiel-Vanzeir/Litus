<?php

namespace QuizBundle\Repository;

use Doctrine\ORM\EntityRepository,
    QuizBundle\Entity\Quiz as QuizEntity;

/**
 * Point
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Point extends EntityRepository
{
    /**
     * Gets all points belonging to a quiz
     * @param QuizBundle\Entity\Quiz $quiz The quiz the points must belong to
     */
    public function findByQuiz(QuizEntity $quiz)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('QuizBundle\Entity\Point', 'p')
            ->innerJoin('p.round', 'r')
            ->innerJoin('p.team', 't')
            ->where(
                $query->expr()->eq('r.quiz', ':quiz')
            )
            ->orderBy('r.order', 'ASC')
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
