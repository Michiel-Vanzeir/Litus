<?php

namespace CudiBundle\Repository\Comment;

use CudiBundle\Entity\Article,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Comment
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Comment extends EntityRepository
{
    public function findAllByArticleQuery(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('c')
            ->from('CudiBundle\Entity\Comment\Mapping', 'm')
            ->from('CudiBundle\Entity\Comment\Comment', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.comment', 'c'),
                    $query->expr()->eq('m.article', ':article')
                )
            )
            ->setParameter('article', $article)
            ->getQuery();

        return $resultSet;
    }

    public function findAllExternalByArticleQuery(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('c')
            ->from('CudiBundle\Entity\Comment\Mapping', 'm')
            ->from('CudiBundle\Entity\Comment\Comment', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('m.comment', 'c'),
                    $query->expr()->eq('m.article', ':article'),
                    $query->expr()->eq('c.type', '\'external\'')
                )
            )
            ->setParameter('article', $article)
            ->getQuery();

        return $comments;
    }

    public function findAllSiteByArticleQuery(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('c')
            ->from('CudiBundle\Entity\Comment\Mapping', 'm')
            ->from('CudiBundle\Entity\Comment\Comment', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('m.comment', 'c'),
                    $query->expr()->eq('m.article', ':article'),
                    $query->expr()->eq('c.type', '\'site\'')
                )
            )
            ->setParameter('article', $article)
            ->getQuery()
            ->getResult();

        return $comments;
    }
}
