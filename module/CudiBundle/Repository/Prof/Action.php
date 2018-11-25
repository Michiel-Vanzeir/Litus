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
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Repository\Prof;

use CommonBundle\Entity\User\Person;

/**
 * Action
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Action extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  integer|null $nbResults
     * @return \Doctrine\ORM\Query
     */
    public function findAllUncompletedQuery($nbResults = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('a.refuseDate'),
                    $query->expr()->isNull('a.confirmDate')
                )
            )
            ->orderBy('a.timestamp', 'ASC')
            ->setMaxResults($nbResults)
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllCompletedQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->isNotNull('a.confirmDate')
            )
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllRefusedQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->isNotNull('a.refuseDate')
            )
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  Person $person
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPersonQuery(Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->eq('a.person', ':person')
            )
            ->setParameter('person', $person->getId())
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string $entity
     * @param  string $action
     * @param  Person $person
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndActionAndPersonQuery($entity, $action, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string  $entity
     * @param  integer $entityId
     * @param  string  $action
     * @param  Person  $person
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndEntityIdAndActionAndPersonQuery($entity, $entityId, $action, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string  $entity
     * @param  integer $entityId
     * @param  Person  $person
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndEntityIdAndPersonQuery($entity, $entityId, Person $person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.person', ':person'),
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId')
                )
            )
            ->setParameter('person', $person->getId())
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string  $entity
     * @param  integer $previousId
     * @param  string  $action
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndPreviousIdAndActionQuery($entity, $previousId, $action)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.previousId', ':previousId'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('entity', $entity)
            ->setParameter('previousId', $previousId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string  $entity
     * @param  integer $entityId
     * @param  string  $action
     * @param  boolean $includeRefused
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndEntityIdAndActionQuery($entity, $entityId, $action, $includeRefused = true)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.entityId', ':entityId'),
                    $query->expr()->eq('a.action', ':action'),
                    $includeRefused ? '1=1' : $query->expr()->isNull('a.refuseDate')
                )
            )
            ->setParameter('entity', $entity)
            ->setParameter('entityId', $entityId)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string $entity
     * @param  string $action
     * @return \Doctrine\ORM\Query
     */
    public function findAllByEntityAndActionQuery($entity, $action)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Prof\Action', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.entity', ':entity'),
                    $query->expr()->eq('a.action', ':action')
                )
            )
            ->setParameter('entity', $entity)
            ->setParameter('action', $action)
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery();
    }
}
