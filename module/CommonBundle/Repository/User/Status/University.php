<?php

namespace CommonBundle\Repository\User\Status;

use CommonBundle\Entity\General\AcademicYear;

/**
 * University
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class University extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  string       $status
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByStatusQuery($status, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('CommonBundle\Entity\User\Status\University', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('s.status', ':status'),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('status', $status)
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery();
    }
}
