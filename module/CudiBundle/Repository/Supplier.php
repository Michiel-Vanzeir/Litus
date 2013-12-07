<?php

namespace CudiBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Supplier
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Supplier extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Supplier', 's')
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }
}
