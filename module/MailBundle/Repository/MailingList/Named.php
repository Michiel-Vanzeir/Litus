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

namespace MailBundle\Repository\MailingList;

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\User\Person\Academic;

/**
 * Named
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Named extends EntityRepository
{
    public function findAllByAdminQuery(Academic $academic)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('l')
            ->from('MailBundle\Entity\MailingList\Named', 'l')
            ->innerJoin('MailBundle\Entity\MailingList\AdminMap', 'a', 'a.list = l.name')
            ->where(
                $query->expr()->eq('a.academic', ':academic')
            )
            ->orderBy('l.name', 'ASC')
            ->setParameter('academic', $academic)
            ->getQuery();

        return $resultSet;
    }
}
