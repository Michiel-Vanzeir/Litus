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

namespace CommonBundle\Repository\User\Status;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear;

/**
 * University
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class University extends EntityRepository
{
    /**
     * @param  string              $status
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByStatusQuery($status, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s')
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

        return $resultSet;
    }
}
