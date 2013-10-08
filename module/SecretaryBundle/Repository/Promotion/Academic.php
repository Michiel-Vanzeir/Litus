<?php

namespace SecretaryBundle\Repository\Promotion;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person\Academic as AcademicPerson,
    Doctrine\ORM\EntityRepository;

/**
 * Academic
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Academic extends EntityRepository
{
    public function findOneByAcademicAndAcademicYear(AcademicPerson $academic, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('SecretaryBundle\Entity\Promotion\Academic', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.academic', ':academic'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
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
}
