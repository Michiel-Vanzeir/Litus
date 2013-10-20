<?php

namespace CudiBundle\Repository\Stock\Period\Value;

use CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Start
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Start extends EntityRepository
{
    public function findOneByArticleAndPeriod(Article $article, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('CudiBundle\Entity\Stock\Period\Value\Start', 'v')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('v.article', ':article'),
                    $query->expr()->eq('v.period', ':period')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('period', $period->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findValueByArticleAndPeriod(Article $article, Period $period)
    {
        $value = $this->findOneByArticleAndPeriod($article, $period);

        if (null == $value)
            return 0;
        return $value->getValue();
    }
}
