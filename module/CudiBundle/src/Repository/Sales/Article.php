<?php

namespace CudiBundle\Repository\Sales;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article as ArticleEntity,
    CudiBundle\Entity\Supplier,
    Doctrine\ORM\EntityRepository,
    Doctrine\ORM\Query\Expr\Join;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends EntityRepository
{
    public function findAllByAcademicYear(AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneByArticleAndAcademicYear(ArticleEntity $article, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.mainArticle', ':article'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
                )
            )
            ->setParameter('article', $article->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

       if (isset($resultSet[0]))
           return $resultSet[0];

       return null;
    }

    public function findOneByBarcode($barcode)
    {
        $start = AcademicYearUtil::getStartOfAcademicYear();
        $start->setTime(0, 0);

        $academicYear = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\AcademicYear')
            ->findOneByUniversityStart($start);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.barcode', ':barcode'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
                )
            )
            ->setParameter('barcode', $barcode)
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findOneByBarcodeAndAcademicYear($barcode, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.barcode', ':barcode'),
                    $query->expr()->eq('a.academicYear', ':academicYear')
                )
            )
            ->setParameter('barcode', $barcode)
            ->setParameter('academicYear', $academicYear->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByTitleAndAcademicYear($title, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where($query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByAuthorAndAcademicYear($author, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.authors'), ':author'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('author', '%'.strtolower($author).'%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByPublisherAndAcademicYear($publisher, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.publishers'), ':publisher'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('publisher', '%'.strtolower($publisher).'%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByBarcodeAndAcademicYear($barcode, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('a.barcode', '\'\''), ':barcode'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('barcode', '%'.$barcode.'%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBySupplierStringAndAcademicYear($supplier, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->innerJoin('a.supplier', 's', Join::WITH,
                $query->expr()->like($query->expr()->lower('s.name'), ':supplier')
            )
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('supplier', '%' . strtolower($supplier) . '%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllBySupplierAndAcademicYear(Supplier $supplier, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllByTitleAndAcademicYearTypeAhead($title, AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sales\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.academicYear', ':academicYear'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false'),
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                        $query->expr()->like($query->expr()->concat('a.barcode', '\'\''), ':barcode')
                    )
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->setParameter('barcode', strtolower($title).'%')
            ->setParameter('academicYear', $academicYear->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
