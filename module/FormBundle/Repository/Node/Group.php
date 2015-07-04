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

namespace FormBundle\Repository\Node;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    DateTime;

/**
 * Group
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Group extends EntityRepository
{
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('n')
            ->from('FormBundle\Entity\Node\Group', 'n')
            ->orderBy('n.creationTime', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllActive()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('g')
            ->from('FormBundle\Entity\Node\Group\Mapping', 'm')
            ->from('FormBundle\Entity\Node\Group', 'g')
            ->innerJoin('m.form', 'f')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('f.endDate', ':now'),
                    $query->expr()->eq('m.group', 'g')
                )
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllOld()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('g')
            ->from('FormBundle\Entity\Node\Group\Mapping', 'm')
            ->from('FormBundle\Entity\Node\Group', 'g')
            ->innerJoin('m.form', 'f')
            ->where(
                $query->expr()->andX(
                    $query->expr()->lt('f.endDate', ':now'),
                    $query->expr()->eq('m.group', 'g')
                )
            )
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
