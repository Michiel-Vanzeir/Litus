<?php

namespace CudiBundle\Repository\Sale;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\AcademicYear,
    CudiBundle\Entity\Article as ArticleEntity,
    CudiBundle\Entity\Supplier,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends EntityRepository
{
    public function findAllByAcademicYearQuery(AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAcademicYearSortBarcode(AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->getQuery()
            ->getResult();

        $barcodes = array();
        foreach($resultSet as $article)
            $barcodes[] = $article->getBarcode();

        array_multisort($barcodes, $resultSet);

        return $resultSet;
    }

    public function findOneByArticle(ArticleEntity $article)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.mainArticle', ':article')
                )
            )
            ->setParameter('article', $article->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOneByBarcode($barcode)
    {
        $barcode = $this->_em
            ->getRepository('CudiBundle\Entity\Sale\Article\Barcode')
            ->findOneByBarcode($barcode);

        if (isset($barcode))
            return $barcode->getArticle();

        return null;
    }

    public function findAllByTypeAndAcademicYearQuery($type, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.type', ':type'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('type', $type)
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByTitleAndAcademicYearQuery($title, AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAuthorAndAcademicYearQuery($author, AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.authors'), ':author'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('author', '%'.strtolower($author).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByTitleOrAuthorAndAcademicYearQuery($string, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, 0);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('m.title'), ':string'),
                        $query->expr()->like($query->expr()->lower('m.authors'), ':string')
                    ),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('string', '%'.strtolower($string).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByPublisherAndAcademicYearQuery($publisher, AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('m.publishers'), ':publisher'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('publisher', '%'.strtolower($publisher).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByBarcodeAndAcademicYearQuery($barcode, AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $articles = $query->select('m.id')
            ->from('CudiBundle\Entity\Sale\Article\Barcode', 'b')
            ->innerJoin('b.article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('barcode', '%'.$barcode.'%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($articles as $id)
            $ids[] = $id['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $ids)
                )
            )
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySupplierStringAndAcademicYearQuery($supplier, AcademicYear $academicYear, $semester = 0)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear, $semester);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->innerJoin('a.supplier', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':supplier'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('supplier', '%' . strtolower($supplier) . '%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllBySupplierQuery(Supplier $supplier)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.supplier', ':supplier'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('m.isHistory', 'false'),
                    $query->expr()->eq('m.isProf', 'false')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByTitleOrBarcodeAndAcademicYearQuery($title, AcademicYear $academicYear)
    {
        $articles = $this->_getArticleIdsBySemester($academicYear);

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a, m')
            ->from('CudiBundle\Entity\Sale\Article', 'a')
            ->from('CudiBundle\Entity\Sale\Article\Barcode', 'b')
            ->innerJoin('a.mainArticle', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('b.article', 'a'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->orX(
                        $query->expr()->like($query->expr()->lower('m.title'), ':title'),
                        $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':title')
                    ),
                    $query->expr()->in('m.id', $articles)
                )
            )
            ->setParameter('title', '%'.strtolower($title).'%')
            ->orderBy('m.title', 'ASC')
            ->getQuery();

        return $resultSet;
    }

    private function _getArticleIdsBySemester(AcademicYear $academicYear, $semester = 0)
    {
        $query = $this->_em->createQueryBuilder();
        if ($semester == 0) {
            $resultSet = $query->select('a.id')
                ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
                ->innerJoin('m.article', 'a')
                ->innerJoin('m.subject', 's')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('a.isHistory', 'false'),
                        $query->expr()->eq('a.isProf', 'false'),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('academicYear', $academicYear->getId())
                ->getQuery()
                ->getResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('a.id')
                ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
                ->innerJoin('m.article', 'a')
                ->innerJoin('m.subject', 's')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->eq('a.isHistory', 'false'),
                        $query->expr()->eq('a.isProf', 'false'),
                        $query->expr()->orX(
                            $query->expr()->eq('s.semester', '0'),
                            $query->expr()->eq('s.semester', ':semester')
                        ),
                        $query->expr()->eq('m.academicYear', ':academicYear')
                    )
                )
                ->setParameter('semester', $semester)
                ->setParameter('academicYear', $academicYear->getId())
                ->getQuery()
                ->getResult();
        }

        $articles = array(0);
        foreach ($resultSet as $item)
            $articles[$item['id']] = $item['id'];

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a.id')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false'),
                    $query->expr()->eq('a.type', '\'common\'')
                )
            )
            ->getQuery()
            ->getResult();

        foreach ($resultSet as $item)
            $articles[$item['id']] = $item['id'];

        return $articles;
    }
}
