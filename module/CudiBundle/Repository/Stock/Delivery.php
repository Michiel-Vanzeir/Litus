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

use CommonBundle\Component\Doctrine\ORM\EntityRepository,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Sale\Article,
    CudiBundle\Entity\Stock\Period as PeriodEntity,
    CudiBundle\Entity\Supplier,
    DateTime;

/**
 * Delivery
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Delivery extends EntityRepository
{
    /**
     * @param  Supplier            $supplier
     * @param  PeriodEntity        $period
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierAndPeriodQuery(Supplier $supplier, PeriodEntity $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Delivery', 'o')
            ->innerJoin('o.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('o.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.timestamp', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    /**
     * @param  PeriodEntity        $period
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPeriodQuery(PeriodEntity $period)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Delivery', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('o.timestamp', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.timestamp', ':endDate')
                )
            )
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.timestamp', 'DESC');

        if (!$period->isOpen()) {
            $query->setParameter('endDate', $period->getEndDate());
        }

        $resultSet = $query->getQuery();

        return $resultSet;
    }

    /**
     * @param  Article      $article
     * @param  AcademicYear $academicYear
     * @return int
     */
    public function findNumberByArticleAndAcademicYear(Article $article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet) {
            return 0;
        }

        return $resultSet;
    }

    /**
     * @param  Supplier     $supplier
     * @param  AcademicYear $academicYear
     * @return int
     */
    public function findNumberBySupplier(Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet) {
            return 0;
        }

        return $resultSet;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return int
     */
    public function getDeliveredAmountByAcademicYear(AcademicYear $academicYear)
    {
        return $this->getDeliveredAmountBetween($academicYear->getStartDate(), $academicYear->getEndDate());
    }

    /**
     * @param  DateTime $startDate
     * @param  DateTime $endDate
     * @return int
     */
    public function getDeliveredAmountBetween(DateTime $startDate, DateTime $endDate)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number * a.purchasePrice)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet) {
            return 0;
        }

        return $resultSet;
    }

    /**
     * @param  AcademicYear $academicYear
     * @return int
     */
    public function getNumberByAcademicYear(AcademicYear $academicYear)
    {
        return $this->getNumberBetween($academicYear->getStartDate(), $academicYear->getEndDate());
    }

    /**
     * @param  DateTime $startDate
     * @param  DateTime $endDate
     * @return int
     */
    public function getNumberBetween(DateTime $startDate, DateTime $endDate)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('SUM(d.number)')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getSingleScalarResult();

        if (null == $resultSet) {
            return 0;
        }

        return $resultSet;
    }

    /**
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $article
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleAndAcademicYearQuery($article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', '%' . strtolower($article) . '%')
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $supplier
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierAndAcademicYearQuery($supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.supplier', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', '%' . strtolower($supplier) . '%')
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  Article             $article
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleEntityQuery(Article $article, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('d.article', ':article'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('article', $article)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  Supplier            $supplier
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySupplierEntityQuery(Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    /**
     * @param  string              $title
     * @param  Supplier            $supplier
     * @param  AcademicYear        $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllByArticleTitleAndSupplierAndAcademicYearQuery($title, Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('d')
            ->from('CudiBundle\Entity\Stock\Delivery', 'd')
            ->innerJoin('d.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->gt('d.timestamp', ':start'),
                    $query->expr()->lt('d.timestamp', ':end')
                )
            )
            ->setParameter('title', '%' . strtolower($title) . '%')
            ->setParameter('supplier', $supplier)
            ->setParameter('start', $academicYear->getStartDate())
            ->setParameter('end', $academicYear->getEndDate())
            ->orderBy('d.timestamp', 'DESC')
            ->getQuery();

        return $resultSet;
    }
}
