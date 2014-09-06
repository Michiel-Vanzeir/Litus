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

namespace BrBundle\Repository;

use CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Product
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Product extends EntityRepository
{
    public function findProductByIdQuery($id)
    {
        $query = $this->_em->createQueryBuilder();
        $result = $query->select('p')
            ->from('BrBundle\Entity\Product', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('p.id', ':id'),
                    $query->expr()->eq('p.old', 'FALSE')
                )
            )
            ->setParameter('id', $id)
            ->getQuery();

        return $result;
    }
}