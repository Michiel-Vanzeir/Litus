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

namespace CudiBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\User\Person;

/**
 * IsicCard
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IsicCard extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('c')
            ->from('CudiBundle\Entity\IsicCard', 'c')
            ->orderBy('c.cardNumber', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  Person              $person
     * @return \Doctrine\ORM\Query
     */
    public function findByPersonQuery($person)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('c')
            ->from('CudiBundle\Entity\IsicCard', 'c')
            ->where($query->expr()->andX(
                    $query->expr()->eq('c.person', ':person')
                )
            )
            ->setParameter('person', $person->getID())
            ->orderBy('c.cardNumber', 'ASC')
            ->getQuery();

        return $resultSet;
    }
}
