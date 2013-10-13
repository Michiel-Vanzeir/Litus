<?php

namespace ApiBundle\Repository;

use DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Key
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Key extends EntityRepository
{
    public function findAllActiveQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('k')
            ->from('ApiBundle\Entity\Key', 'k')
            ->where(
                $query->expr()->gt('k.expirationTime', ':now')
            )
            ->setParameter('now', new DateTime())
            ->getQuery();

        return $resultSet;
    }

    public function findOneActiveByCode($code)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('k')
            ->from('ApiBundle\Entity\Key', 'k')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('k.code', ':code'),
                    $query->expr()->gt('k.expirationTime', ':now')
                )
            )
            ->setParameter('code', $code)
            ->setParameter('now', new DateTime())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
