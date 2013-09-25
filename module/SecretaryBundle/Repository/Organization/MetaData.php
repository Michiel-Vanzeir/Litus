<?php

namespace SecretaryBundle\Repository\Organization;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic,
    Doctrine\ORM\EntityRepository;

/**
 * MetaData
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MetaData extends EntityRepository
{
    public function findOneByAcademicAndAcademicYear(Academic $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SecretaryBundle\Entity\Organization\MetaData', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.academic', ':academic'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academic', $academic)
            ->setParameter('academicYear', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllBakskeByAcademicYear(AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SecretaryBundle\Entity\Organization\MetaData', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.bakskeByMail', 'true'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
