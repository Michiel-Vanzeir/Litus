<?php

namespace QuizBundle\Repository;

use QuizBundle\Entity\Quiz as QuizEntity;

/**
 * Round
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Round extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * Gets all rounds belonging to a quiz
     * @param QuizEntity $quiz The quiz the rounds must belong to
     */
    public function findAllByQuizQuery(QuizEntity $quiz)
    {
        $query = $this->getEntityManager()->createQueryBuilder();

        return $query->select('r')
            ->from('QuizBundle\Entity\Round', 'r')
            ->where(
                $query->expr()->eq('r.quiz', ':quiz')
            )
            ->orderBy('r.order', 'ASC')
            ->setParameter('quiz', $quiz)
            ->getQuery();
    }

    /**
     * Gets the order for the next round in the quiz
     * @param  QuizEntity $quiz
     * @return integer
     */
    public function getNextRoundOrderForQuiz(QuizEntity $quiz)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('MAX(r.order)')
            ->from('QuizBundle\Entity\Round', 'r')
            ->where(
                $query->expr()->eq('r.quiz', ':quiz')
            )
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getSingleScalarResult();

        if ($resultSet === null) {
            return 1;
        }

        return $resultSet + 1;
    }
}
