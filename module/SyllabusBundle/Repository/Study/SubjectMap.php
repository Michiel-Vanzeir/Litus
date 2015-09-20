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

namespace SyllabusBundle\Repository\Study;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Query,
    SyllabusBundel\Entity\Study\SubjectMap as SubjectMapEntity,
    SyllabusBundle\Entity\Study as StudyEntity,
    SyllabusBundle\Entity\Study\ModuleGroup as ModuleGroupEntity,
    SyllabusBundle\Entity\Subject as SubjectEntity;

/**
 * StudySubjectMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubjectMap extends EntityRepository
{
    /**
	 * @param  StudyEntity $study
	 * @return Query
	 */
    public function findAllByStudyQuery(StudyEntity $study)
    {
        $moduleGroups = $this->getModuleGroupIds($study->getCombination()->getModuleGroups()->toArray());

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $study->getAcademicYear())
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  ModuleGroupEntity $moduleGroup
	 * @param  AcademicYear $academicYear
	 * @return Query
	 */
    public function findAllByModuleGroupAndAcademicYearQuery(ModuleGroupEntity $moduleGroup, AcademicYear $academicYear)
    {
        $moduleGroups = $this->getModuleGroupIds(array($moduleGroup));

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $name
	 * @param  StudyEntity $study
	 * @return Query
	 */
    public function findAllByNameAndStudyQuery($name, StudyEntity $study)
    {
        $moduleGroups = $this->getModuleGroupIds($study->getCombination()->getModuleGroups()->toArray());

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $study->getAcademicYear())
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $code
	 * @param  StudyEntity $study
	 * @return Query
	 */
    public function findAllByCodeAndStudyQuery($code, StudyEntity $study)
    {
        $moduleGroups = $this->getModuleGroupIds($study->getCombination()->getModuleGroups()->toArray());

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.code'), ':code'),
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->setParameter('academicYear', $study->getAcademicYear())
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $name
	 * @param  ModuleGroupEntity $moduleGroup
	 * @param  AcademicYear $academicYear
	 * @return Query
	 */
    public function findAllByNameAndModuleGroupAndAcademicYearQuery($name, ModuleGroupEntity $moduleGroup, AcademicYear $academicYear)
    {
        $moduleGroups = $this->getModuleGroupIds(array($moduleGroup));

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  string $code
	 * @param  ModuleGroupEntity $moduleGroup
	 * @param  AcademicYear $academicYear
	 * @return Query
	 */
    public function findAllByCodeAndModuleGroupAndAcademicYearQuery($code, ModuleGroupEntity $moduleGroup, AcademicYear $academicYear)
    {
        $moduleGroups = $this->getModuleGroupIds(array($moduleGroup));

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.code'), ':code'),
                    $query->expr()->in('m.moduleGroup', $moduleGroups),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  array $groups
	 * @return array
	 */
    private function getModuleGroupIds(array $groups)
    {
        $ids = array(0);

        if (sizeof($groups) == 0) {
            return $ids;
        }

        foreach ($groups as $group) {
            $ids[] = $group->getId();

            $ids = array_merge($ids, $this->getModuleGroupIds($group->getChildren()->toArray()));
        }

        return array_unique($ids);
    }

    /**
	 * @param  SubjectEntity $subject
	 * @param  AcademicYear $academicYear
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllBySubjectAndAcademicYearQuery(SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('subject', $subject)
            ->setParameter('academicYear', $academicYear)
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param  ModuleGroupEntity $moduleGroup
	 * @param  SubjectEntity $subject
	 * @param  AcademicYear $academicYear
	 * @return SubjectMapEntity
	 */
    public function findOneByModuleGroupSubjectAndAcademicYear(ModuleGroupEntity $moduleGroup, SubjectEntity $subject, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('m.moduleGroup', ':moduleGroup'),
                    $query->expr()->in('m.subject', ':subject'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('moduleGroup', $moduleGroup)
            ->setParameter('subject', $subject)
            ->setParameter('academicYear', $academicYear)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    /**
	 * @param  string $name
	 * @param  AcademicYear $academicYear
	 * @return \Doctrine\ORM\Query
	 */
    public function findAllByNameQuery($name, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m', 's')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('s.name')
            ->getQuery();

        return $resultSet;
    }

    /**
	 * @param string $name
	 * @param AcademicYear $academicYear
	 * @return Query
	 */
    public function findAllSubjectsByNameQuery($name, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('s.id as id, s.name as name, s.code as code')
            ->from('SyllabusBundle\Entity\Study\SubjectMap', 'm')
            ->innerJoin('m.subject', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                        $query->expr()->like($query->expr()->lower('s.code'), ':code')
                    ),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setParameter('code', '%' . strtolower($name) . '%')
            ->setParameter('academicYear', $academicYear)
            ->orderBy('s.name')
            ->distinct()
            ->getQuery();

        return $resultSet;
    }
}
