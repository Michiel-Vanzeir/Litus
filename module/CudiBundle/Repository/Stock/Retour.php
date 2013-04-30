<?php

namespace CudiBundle\Repository\Stock;

use CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Stock\Period as PeriodEntity,
    CudiBundle\Entity\Supplier,
    Doctrine\ORM\EntityRepository;

/**
 * Retour
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Retour extends EntityRepository
{
    public function findAllBySupplierAndPeriod(Supplier $supplier, PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
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

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findTotalByArticleAndPeriod(Article $article, PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
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

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getSingleScalarResult();

        return $resultSet ? $resultSet : 0;
    }

    public function findAllByPeriod(PeriodEntity $period)
    {
        $query = $this->_em->createQueryBuilder();
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

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }
}