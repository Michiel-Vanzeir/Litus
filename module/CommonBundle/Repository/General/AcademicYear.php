<?php

namespace CommonBundle\Repository\General;

use DateInterval;

/**
 * AcademicYear
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AcademicYear extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  integer $id
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->where(
                $query->expr()->eq('y.id', ':id')
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('y')
            ->from('CommonBundle\Entity\General\AcademicYear', 'y')
            ->orderBy('y.universityStart', 'DESC')
            ->getQuery();
    }

    /**
     * @param  \DateTime $date
     * @return \CommonBundle\Entity\General\AcademicYear|null
     */
    public function findOneByDate($date)
    {
        $datePreviousYear = clone $date;
        $datePreviousYear = $datePreviousYear->sub(new DateInterval('P1Y'));

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('y')
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
    }
}
