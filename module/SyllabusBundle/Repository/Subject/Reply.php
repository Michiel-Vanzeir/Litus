<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Repository\Subject;

use SyllabusBundle\Entity\Subject\Comment as CommentEntity,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Reply
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Reply extends EntityRepository
{
    public function findLastQuery($nb = 10)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SyllabusBundle\Entity\Subject\Reply', 'r')
            ->innerJoin('r.comment', 'c')
            ->where(
                $query->expr()->isNull('c.readBy')
            )
            ->orderBy('r.date', 'DESC')
            ->setMaxResults($nb)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByCommentQuery(CommentEntity $comment)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SyllabusBundle\Entity\Subject\Reply', 'r')
            ->where(
                $query->expr()->eq('r.comment', ':comment')
            )
            ->orderBy('r.date', 'ASC')
            ->setParameter('comment', $comment)
            ->getQuery();

        return $resultSet;
    }

    public function findLastByComment(CommentEntity $comment)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('SyllabusBundle\Entity\Subject\Reply', 'r')
            ->where(
                $query->expr()->eq('r.comment', ':comment')
            )
            ->orderBy('r.date', 'DESC')
            ->setParameter('comment', $comment)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
