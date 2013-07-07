<?php

namespace ShiftBundle\Repository\Shift;

use Doctrine\ORM\EntityRepository;

/**
 * Responsible
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Responsible extends EntityRepository
{
    public function findOneById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('v')
            ->from('ShiftBundle\Entity\Shift\Responsible', 'v')
            ->where(
                $query->expr()->eq('v.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];
        return null;
    }
}