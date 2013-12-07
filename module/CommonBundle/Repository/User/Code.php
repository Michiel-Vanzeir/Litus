<?php

namespace CommonBundle\Repository\User;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Code
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Code extends EntityRepository
{
    public function findOnePersonByCode($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person', 'p')
            ->innerJoin('p.code', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('c.code', ':code'),
                    $query->expr()->orX(
                        $query->expr()->gt('c.expirationTime', ':now'),
                        $query->expr()->isNull('c.expirationTime')
                    )
                )
            )
            ->setParameter('code', $code)
            ->setParameter('now', new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
