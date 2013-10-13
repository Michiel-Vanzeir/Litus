<?php

namespace SyllabusBundle\Repository;

use CommonBundle\Component\Util\AcademicYear as UtilAcademicYear,
    CommonBundle\Entity\User\Person,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * Subject
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subject extends EntityRepository
{
    public function findAllByNameAndAcademicYearTypeAhead($name, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\StudySubjectMap', 'm')
            ->innerJoin('m.subject', 's', Join::WITH,
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                        $query->expr()->like($query->expr()->lower('s.code'), ':name')
                    )
                )
            )
            ->where(
                $query->expr()->eq('m.academicYear', ':academicYear')
            )
            ->setParameter('academicYear', $academicYear->getId())
            ->setParameter('name', strtolower(trim($name)) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();

        $subjects = array();
        foreach($resultSet as $map)
            $subjects[$map->getSubject()->getId()] = $map->getSubject();

        return $subjects;
    }

    public function getYearsByPerson(Person $person)
    {
        $years = array();

        $startAcademicYear = UtilAcademicYear::getStartOfAcademicYear();
        $startAcademicYear->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($startAcademicYear);

        $studies = $this->_em->getRepository('SecretaryBundle\Entity\Syllabus\StudyEnrollment')
            ->findAllByAcademicAndAcademicYear($person, $academicYear);

        foreach($studies as $studyMap) {
            $year = $studyMap->getStudy()->getPhase();
            if (strpos(strtolower($studyMap->getStudy()->getFullTitle()), 'master') !== false)
                $years[$year+3] = $year+3;
            elseif (strpos(strtolower($studyMap->getStudy()->getFullTitle()), 'bachelor') !== false)
                $years[$year] = $year;
        }

        return $years;
    }
}
