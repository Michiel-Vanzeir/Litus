<?php

namespace CudiBundle\Repository\Article;

use CudiBundle\Entity\Article,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * History
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class History extends EntityRepository
{
    public function findAllByArticleQuery(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('h, a')
            ->from('CudiBundle\Entity\Article\History', 'h')
            ->innerJoin('h.precursor', 'a')
            ->where(
                $query->expr()->eq('h.article', ':article')
            )
            ->setParameter('article', $article)
            ->orderBy('a.timestamp')
            ->getQuery();

        return $resultSet;
    }
}
