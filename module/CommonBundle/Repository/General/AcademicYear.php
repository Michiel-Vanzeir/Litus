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

namespace CommonBundle\Repository\General;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateInterval;

/**
 * AcademicYear
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AcademicYear extends EntityRepository
{
    /**
     * @param  int                                            $id
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->where(
                $query->expr()->eq('y.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->orderBy('y.universityStart', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  dateTime                                       $date
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    public function findOneByDate($date)
    {
        $datePreviousYear = clone $date;
        $datePreviousYear = $datePreviousYear->sub( new DateInterval('P1Y'));

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->where(
                $query->expr()->andX(
                    $query->expr()->lt('y.start', ':date'),
                    $query->expr()->gt('y.start', ':datePreviousYear')
                )
            )
            ->setParameter('date', $date)
            ->setParameter('datePreviousYear', $datePreviousYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }
}
