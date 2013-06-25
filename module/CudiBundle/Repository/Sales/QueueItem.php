<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Entity\User\Person,
    CudiBundle\Entity\Sale\Session as SessionEntity,
    Doctrine\ORM\EntityRepository;

/**
 * QueueItem
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class QueueItem extends EntityRepository
{
    public function getNextQueueNumber(SessionEntity $session)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('MAX(i.queueNumber)')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where($query->expr()->eq('i.session', ':session'))
            ->setParameter('session', $session->getId())
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0][1] + 1;

        return 1;
    }

    public function findOneByPersonNotSold(SessionEntity $session, Person $person)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where($query->expr()->andX(
                    $query->expr()->eq('i.session', ':session'),
                    $query->expr()->eq('i.person', ':person'),
                    $query->expr()->neq('i.status', ':sold')
                )
            )
            ->setParameter('session', $session->getId())
            ->setParameter('person', $person->getId())
            ->setParameter('sold', 'sold')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByStatus(SessionEntity $session, $status)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where($query->expr()->andX(
                    $query->expr()->eq('i.session', ':session'),
                    $query->expr()->eq('i.status', ':status')
                )
            )
            ->setParameter('session', $session->getId())
            ->setParameter('status', $status)
            ->orderBy('i.queueNumber', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBySession(SessionEntity $session)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where($query->expr()->andX(
                    $query->expr()->eq('i.session', ':session'),
                    $query->expr()->neq('i.status', ':sold'),
                    $query->expr()->neq('i.status', ':cancelled')
                )
            )
            ->setParameter('session', $session->getId())
            ->setParameter('sold', 'sold')
            ->setParameter('cancelled', 'cancelled')
            ->orderBy('i.queueNumber', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findNbBySession(SessionEntity $session)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('COUNT(i)')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where($query->expr()->andX(
                    $query->expr()->eq('i.session', ':session'),
                    $query->expr()->neq('i.status', ':sold'),
                    $query->expr()->neq('i.status', ':cancelled'),
                    $query->expr()->neq('i.status', ':hold')
                )
            )
            ->setParameter('session', $session->getId())
            ->setParameter('sold', 'sold')
            ->setParameter('cancelled', 'cancelled')
            ->setParameter('hold', 'hold')
            ->getQuery()
            ->getSingleScalarResult();

        return $resultSet;
    }

    public function findOneSoldByPersonAndSession(Person $person, SessionEntity $session)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Sale\QueueItem', 'i')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('i.person', ':person'),
                    $query->expr()->eq('i.session', ':session'),
                    $query->expr()->eq('i.status', ':sold')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('session', $session)
            ->setParameter('sold', 'sold')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }
}
