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

namespace LogisticsBundle\Repository\Reservation;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * ReservableResource
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReservableResource extends EntityRepository
{
    public function findOneByName($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\ReservableResource', 'r')
            ->where(
                $query->expr()->eq('r.name', ':name')
            )
            ->setParameter('name', $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('r')
            ->from('LogisticsBundle\Entity\Reservation\ReservableResource', 'r')
            ->getQuery();

        return $resultSet;
    }
}
