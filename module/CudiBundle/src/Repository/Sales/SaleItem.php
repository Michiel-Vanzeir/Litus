<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article as ArticleEntity,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * SaleItem
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SaleItem extends EntityRepository
{
    public function findOneByPersonAndArticle(Person $person, ArticleEntity $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Sales\SaleItem', 'i')
            ->innerJoin('i.queueItem', 'q', Join::WITH,
                $query->expr()->eq('q.person', ':person')
            )
            ->where(
                   $query->expr()->eq('i.article', ':article')
            )
            ->setParameter('person', $person->getId())
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];
    }
}
