<?php

namespace CudiBundle\Repository\Stock\Order;

use CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Stock\Order\Order as OrderEntity,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\OrderBy;

/**
 * Item
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Item extends EntityRepository
{
    public function findOneOpenByArticle(Article $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('i.article', ':article'),
                    $query->expr()->isNull('o.dateCreated')
                )
            )
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByPeriod(Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('o.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateCreated', ':endDate')
                )
            )
            ->orderBy('o.dateOrdered', 'DESC')
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByTitleAndPeriod($title, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->gt('o.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateCreated', ':endDate')
                )
            )
            ->orderBy('o.dateOrdered', 'DESC')
            ->setParameter('title', '%'.strtolower($title).'%')
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBySupplierStringAndPeriod($supplier, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->innerJoin('o.supplier', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->gt('o.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateCreated', ':endDate')
                )
            )
            ->orderBy('o.dateOrdered', 'DESC')
            ->setParameter('supplier', '%'.strtolower($supplier).'%')
            ->setParameter('startDate', $period->getStartDate());

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByOrderAlpha(OrderEntity $order)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->eq('i.order', ':order')
            )
            ->setParameter('order', $order)
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByOrderOnBarcode(OrderEntity $order)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('i')
            ->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.barcodes', 'b')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('i.order', ':order'),
                    $query->expr()->eq('b.main', ':isMainBarcode')
                )
            )
            ->setParameter('order', $order)
            ->setParameter('isMainBarcode', 'true')
            ->orderBy('b.barcode', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllPaginator($currentPage, $itemsPerPage, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('start', $academicYear->getUniversityStartDate())
            ->setParameter('end', $academicYear->getUniversityEndDate());

        return $this->_findAllPaginator($currentPage, $itemsPerPage, $query, new OrderBy('o.dateOrdered', 'DESC'));
    }

    private function _findAllPaginator($currentPage, $itemsPerPage, $basicQuery, $order)
    {
        $currentPage = $currentPage == 0 ? $currentPage = 1 : $currentPage;

        $query = clone $basicQuery;
        $resultSet = $query->select('i')
            ->setMaxResults($itemsPerPage)
            ->setFirstResult(($currentPage - 1) * $itemsPerPage)
            ->orderBy($order)
            ->getQuery()
            ->getResult();

        $query = clone $basicQuery;
        $totalNumber = $query->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return array($resultSet, $totalNumber);
    }

    public function findAllByArticlePaginator($article, $currentPage, $itemsPerPage, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':article'),
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('article', '%'.strtolower($article).'%')
            ->setParameter('start', $academicYear->getUniversityStartDate())
            ->setParameter('end', $academicYear->getUniversityEndDate());

        return $this->_findAllPaginator($currentPage, $itemsPerPage, $query, new OrderBy('o.dateOrdered', 'DESC'));
    }

    public function findAllBySupplierPaginator($supplier, $currentPage, $itemsPerPage, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.supplier', 's')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('supplier', '%'.strtolower($supplier).'%')
            ->setParameter('start', $academicYear->getUniversityStartDate())
            ->setParameter('end', $academicYear->getUniversityEndDate());

        return $this->_findAllPaginator($currentPage, $itemsPerPage, $query, new OrderBy('o.dateOrdered', 'DESC'));
    }

    public function findAllByOrderPaginator(OrderEntity $order, $currentPage, $itemsPerPage, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.id', ':order'),
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('order', $order)
            ->setParameter('start', $academicYear->getUniversityStartDate())
            ->setParameter('end', $academicYear->getUniversityEndDate());

        return $this->_findAllPaginator($currentPage, $itemsPerPage, $query, new OrderBy('o.dateOrdered', 'DESC'));
    }

    public function findAllByArticleAndOrderPaginator($article, OrderEntity $order, $currentPage, $itemsPerPage, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->from('CudiBundle\Entity\Stock\Order\Item', 'i')
            ->innerJoin('i.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->innerJoin('i.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':article'),
                    $query->expr()->eq('o.id', ':order'),
                    $query->expr()->isNotNull('o.dateOrdered'),
                    $query->expr()->gt('o.dateOrdered', ':start'),
                    $query->expr()->lt('o.dateOrdered', ':end')
                )
            )
            ->setParameter('article', '%'.strtolower($article).'%')
            ->setParameter('order', $order)
            ->setParameter('start', $academicYear->getUniversityStartDate())
            ->setParameter('end', $academicYear->getUniversityEndDate());

        return $this->_findAllPaginator($currentPage, $itemsPerPage, $query, new OrderBy('o.dateOrdered', 'DESC'));
    }
}
