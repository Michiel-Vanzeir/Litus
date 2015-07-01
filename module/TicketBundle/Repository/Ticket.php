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

namespace TicketBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person,
    TicketBundle\Entity\Event as EventEntity;

/**
 * Ticket
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Ticket extends EntityRepository
{
    public function findOneByEventAndNumber(EventEntity $event, $number)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->eq('t.number', ':number')
                )
            )
            ->setParameter('event', $event)
            ->setParameter('number', $number)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllByEventQuery(EventEntity $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->eq('t.event', ':event')
            )
            ->setParameter('event', $event)
            ->getQuery();

        return $resultSet;
    }

    public function findAllByEventAndPersonQuery(EventEntity $event, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.person', ':person'),
                    $query->expr()->eq('t.event', ':event')
                )
            )
            ->setParameter('person', $person)
            ->setParameter('event', $event)
            ->getQuery();

        return $resultSet;
    }

    public function findAllEmptyByEventQuery(EventEntity $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->eq('t.status', ':empty')
                )
            )
            ->setParameter('event', $event)
            ->setParameter('empty', 'empty')
            ->getQuery();

        return $resultSet;
    }

    public function findAllActiveByEvent(EventEntity $event)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->orX(
                        $query->expr()->eq('t.status', ':booked'),
                        $query->expr()->eq('t.status', ':sold')
                    )
                )
            )
            ->setParameter('event', $event)
            ->setParameter('booked', 'booked')
            ->setParameter('sold', 'sold')
            ->getQuery()
            ->getResult();

        $tickets = array();
        foreach ($resultSet as $ticket) {
            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
        }

        ksort($tickets);

        return $tickets;
    }

    public function findAllByEventAndPersonName(EventEntity $event, $name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->leftJoin('t.guestInfo', 'g')
            ->leftJoin('t.person', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->orX(
                        $query->expr()->eq('t.status', ':booked'),
                        $query->expr()->eq('t.status', ':sold')
                    ),
                    $query->expr()->orX(
                        $query->expr()->orX(
                            $query->expr()->like(
                                $query->expr()->concat(
                                    $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                                    $query->expr()->lower('p.lastName')
                                ),
                                ':name'
                            ),
                            $query->expr()->like(
                                $query->expr()->concat(
                                    $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                                    $query->expr()->lower('p.firstName')
                                ),
                                ':name'
                            )
                        ),
                        $query->expr()->orX(
                            $query->expr()->like(
                                $query->expr()->concat(
                                    $query->expr()->lower($query->expr()->concat('g.firstName', "' '")),
                                    $query->expr()->lower('g.lastName')
                                ),
                                ':name'
                            ),
                            $query->expr()->like(
                                $query->expr()->concat(
                                    $query->expr()->lower($query->expr()->concat('g.lastName', "' '")),
                                    $query->expr()->lower('g.firstName')
                                ),
                                ':name'
                            )
                        )
                    )
                )
            )
            ->setParameter('event', $event)
            ->setParameter('booked', 'booked')
            ->setParameter('sold', 'sold')
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery()
            ->getResult();

        $tickets = array();
        foreach ($resultSet as $ticket) {
            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
        }

        ksort($tickets);

        return $tickets;
    }

    public function findAllByEventAndOption(EventEntity $event, $option)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->leftJoin('t.option', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->orX(
                        $query->expr()->eq('t.status', ':booked'),
                        $query->expr()->eq('t.status', ':sold')
                    ),
                    $query->expr()->like($query->expr()->lower('o.name'), ':option')
                )
            )
            ->setParameter('event', $event)
            ->setParameter('booked', 'booked')
            ->setParameter('sold', 'sold')
            ->setParameter('option', '%' . strtolower($option) . '%')
            ->getQuery()
            ->getResult();

        $tickets = array();
        foreach ($resultSet as $ticket) {
            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
        }

        ksort($tickets);

        return $tickets;
    }

    public function findAllByEventAndOrganization(EventEntity $event, $organization, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p.id')
            ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
            ->innerJoin('m.academic', 'p')
            ->innerJoin('m.organization', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.academicYear', ':academicYear'),
                    $query->expr()->like($query->expr()->lower('o.name'), ':organization')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->setParameter('organization', '%' . strtolower($organization) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($resultSet as $item) {
            $ids[] = $item['id'];
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('t')
            ->from('TicketBundle\Entity\Ticket', 't')
            ->innerJoin('t.person', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('t.event', ':event'),
                    $query->expr()->orX(
                        $query->expr()->eq('t.status', ':booked'),
                        $query->expr()->eq('t.status', ':sold')
                    ),
                    $query->expr()->in('p.id', $ids)
                )
            )
            ->setParameter('event', $event)
            ->setParameter('booked', 'booked')
            ->setParameter('sold', 'sold')
            ->getQuery()
            ->getResult();

        $tickets = array();
        foreach ($resultSet as $ticket) {
            $tickets[$ticket->getFullName() . '-' . $ticket->getId()] = $ticket;
        }

        ksort($tickets);

        return $tickets;
    }
}
