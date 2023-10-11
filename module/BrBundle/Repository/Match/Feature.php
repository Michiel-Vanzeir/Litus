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

namespace BrBundle\Repository\Match;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Feature extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{

    // DOESNT WORK
//    /**
//     * @param Feature_ $feature
//     * @return array
//     */
//    public function findAllBonusIdsByFeature(Feature_ $feature)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $bonus2 = $query->select('b.id')
//            ->from('BrBundle\Entity\Connection\Feature', 'f')
//            ->innerJoin('f.bonus2', 'b')
//            ->where(
//                $query->expr()->eq('m.bonus2', ':feature')
//            )
//            ->setParameter('feature', $feature)
//            ->getQuery()
//            ->getResult();
//
//        return $bonus2;
//    }
//
//    /**
//     * @param Feature_ $feature
//     * @return array
//     */
//    public function findAllMalusIdsByFeature(Feature_ $feature)
//    {
//        $query = $this->getEntityManager()->createQueryBuilder();
//        $bonus1 = $query->select('b.id')
//            ->from('match_feature_bonus_map', 'm')
//            ->innerJoin('m.malus1', 'b')
//            ->where(
//                $query->expr()->eq('m.malus1', ':feature')
//            )
//            ->setParameter('feature', $feature)
//            ->getQuery()
//            ->getResult();
//
//        return $bonus1;
//    }
}
