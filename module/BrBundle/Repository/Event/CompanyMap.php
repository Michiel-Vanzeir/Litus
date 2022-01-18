<?php

namespace BrBundle\Repository\Event;

use BrBundle\Entity\Company;
use BrBundle\Entity\Event;

/**
 * CompanyMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findAllByEvent(Event $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('BrBundle\Entity\Event\CompanyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.event', ':event')
                )
            )
            ->setParameter('event', $event->getId())
            ->getQuery()
            ->getResult();
    }

    public function findAllByEventQuery(Event $event)
        {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('BrBundle\Entity\Event\CompanyMap', 'm')
            ->where(
                $query->expr()->andX(
                         $query->expr()->eq('m.event', ':event')
                )
            )
            ->setParameter('event', $event->getId())
            ->getQuery();
    }

    public function findAllByEventSortedByCompanyQuery(Event $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m','c')
            ->from('BrBundle\Entity\Event\CompanyMap', 'm')
            ->join('m.company', 'c')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.event', ':event')
                )
            )
            ->orderBy('c.name')
            ->setParameter('event', $event->getId())
            ->getQuery();
    }


    public function findByEventAndCompany(Event $event, Company $company)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('BrBundle\Entity\Event\CompanyMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.event', ':event'),
                    $query->expr()->eq('m.company', ':company')
                )
            )
            ->setParameter('event', $event->getId())
            ->setParameter('company', $company->getId())
            ->getQuery()
            ->getResult();
    }
}
