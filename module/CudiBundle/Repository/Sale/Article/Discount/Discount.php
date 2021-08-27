<?php

namespace CudiBundle\Repository\Sale\Article\Discount;

use CommonBundle\Entity\General\Organization;
use CudiBundle\Entity\Sale\Article;

/**
 * Discount
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Discount extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Article $article
     * @param  string  $type
     * @return \CudiBundle\Entity\Sale\Article\Discount\Discount|null
     */
    public function findOneByArticleAndType(Article $article, $type)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->leftJoin('d.template', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->orX(
                        $query->expr()->eq('d.type', ':type'),
                        $query->expr()->eq('t.type', ':type')
                    )
                )
            )
            ->setParameter('article', $article)
            ->setParameter('type', $type)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  Article           $article
     * @param  string            $type
     * @param  Organization|null $organization
     * @return \CudiBundle\Entity\Sale\Article\Discount\Discount|null
     */
    public function findOneByArticleAndTypeAndOrganization(Article $article, $type, Organization $organization = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->leftJoin('d.template', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->orX(
                        $query->expr()->eq('d.type', ':type'),
                        $query->expr()->eq('t.type', ':type')
                    ),
                    $query->expr()->orX(
                        $organization == null ? $query->expr()->isNull('d.organization') : $query->expr()->eq('d.organization', ':organization'),
                        $organization == null ? $query->expr()->isNull('t.organization') : $query->expr()->eq('t.organization', ':organization')
                    )
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('type', $type);

        if ($organization != null) {
            $query->setParameter('organization', $organization);
        }

        return $query->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  Article $article
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleQuery(Article $article)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('d')
            ->from('CudiBundle\Entity\Sale\Article\Discount\Discount', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('article', $article)
            ->getQuery();
    }
}
