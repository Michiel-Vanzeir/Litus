<?php

namespace CudiBundle\Repository\Log\Article\SubjectMap;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Removed
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Removed extends EntityRepository
{
    public function findAllAfter(DateTime $date)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('l')
            ->from('CudiBundle\Entity\Log\Article\SubjectMap\Removed', 'l')
            ->where(
                $query->expr()->gt('l.timestamp', ':date')
            )
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
