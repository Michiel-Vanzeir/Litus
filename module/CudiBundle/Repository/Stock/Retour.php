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

namespace CudiBundle\Repository\Stock;

use CudiBundle\Entity\Sale\Article;
use CudiBundle\Entity\Stock\Period as PeriodEntity;
use CudiBundle\Entity\Supplier;

/**
 * Retour
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Retour extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @param  Supplier     $supplier
     * @param  PeriodEntity $period
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, PeriodEntity $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('r')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->innerJoin('r.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('r.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        return $query->getQuery();
    }

    /**
     * @param  Article      $article
     * @param  PeriodEntity $period
     * @return integer
     */
    public function findTotalByArticleAndPeriod(Article $article, PeriodEntity $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('SUM(r.number)')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('r.article', ':article'),
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param  PeriodEntity $period
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPeriodQuery(PeriodEntity $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('r')
            ->from('CudiBundle\Entity\Stock\Retour', 'r')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('r.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('r.timestamp', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('r.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        return $query->getQuery();
    }
}
