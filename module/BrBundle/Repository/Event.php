<?php

namespace BrBundle\Repository;

use DateTime;

/**
 * Event
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Event extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->getQuery();
    }

    public function findAllActiveQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->gte('r.endDate', ':start')
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery();
    }

    public function findAllVisibleQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->gte('r.endDateVisible', ':start')
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery();
    }

    public function findAllOldQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->lt('r.endDate', ':start')
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery();
    }

    public function findAllByDatesQuery(DateTime $start, DateTime $end)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->orx(
                    $query->expr()->andx(
                        $query->expr()->gte('r.startDate', ':start'),
                        $query->expr()->lte('r.startDate', ':end')
                    ),
                    $query->expr()->andx(
                        $query->expr()->gte('r.endDate', ':start'),
                        $query->expr()->lte('r.endDate', ':end')
                    )
                )
            )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery();
    }

    public function findAllActiveCareerQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('r.endDate', ':start'),
                    $query->expr()->eq('r.visibleForStudents', 'true')
                )
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery();
    }

    public function findAllActiveCorporateQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('r')
            ->from('BrBundle\Entity\Event', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('r.endDate', ':start'),
                    $query->expr()->eq('r.visibleForCompanies', 'true')
                )
            )
            ->setParameter('start', new DateTime())
            ->orderBy('r.startDate')
            ->getQuery();
    }
}
