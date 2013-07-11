<?php

namespace TicketBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('e')
            ->from('TicketBundle\Entity\Event', 'e')
            ->where(
                $query->expr()->eq('e.active', 'true')
            )
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
