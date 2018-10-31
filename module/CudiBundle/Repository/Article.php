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

namespace CudiBundle\Repository;

use CommonBundle\Entity\General\AcademicYear;
use CommonBundle\Entity\User\Person;

/**
 * Article
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Article extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->orderBy('a.title', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $title
     * @return \Doctrine\ORM\Query
     */
    public function findAllByTitleQuery($title)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.title'), ':title'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('title', '%' . strtolower($title) . '%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $author
     * @return \Doctrine\ORM\Query
     */
    public function findAllByAuthorQuery($author)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.authors'), ':author'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('author', '%' . strtolower($author) . '%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $isbn
     * @return \Doctrine\ORM\Query
     */
    public function findAllByIsbnQuery($isbn)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->concat('a.isbn', '\'\''), ':isbn'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('isbn', '%' . strtolower($isbn) . '%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $publisher
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPublisherQuery($publisher)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.publishers'), ':publisher'),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->setParameter('publisher', '%' . strtolower($publisher) . '%')
            ->orderBy('a.title', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string       $subject
     * @param  AcademicYear $academicYear
     * @return \Doctrine\ORM\Query
     */
    public function findAllBySubjectQuery($subject, AcademicYear $academicYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $subjects = $query->select('s.id')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->orX(
                    $query->expr()->like($query->expr()->lower('s.name'), ':name'),
                    $query->expr()->like($query->expr()->lower('s.code'), ':name')
                )
            )
            ->setParameter('name', strtolower(trim($subject)) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($subjects as $subject) {
            $ids[] = $subject['id'];
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('a.id')
            ->from('CudiBundle\Entity\Article\SubjectMap', 's')
            ->innerJoin('s.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('s.subject', $ids),
                    $query->expr()->eq('s.academicYear', ':academicYear')
                )
            )
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $articles = array(0);
        foreach ($resultSet as $mapping) {
            $articles[] = $mapping->getArticle()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('a.id', $articles),
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->eq('a.isProf', 'false')
                )
            )
            ->getQuery();
    }

    /**
     * @param  Person $person
     * @return array
     */
    public function findAllByProf(Person $person)
    {
        $ids = array_merge(
            $this->getCurrentArticleIdsByProf($person),
            $this->getAddedArticleIdsByProf($person)
        );

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('CudiBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('a.isHistory', 'false'),
                    $query->expr()->in('a.id', $ids),
                    $query->expr()->notIn('a.id', $this->getRemovedArticleIds())
                )
            )
            ->orderBy('a.title', 'ASC')
            ->getQuery()
            ->getResult();

        $articles = array();
        foreach ($resultSet as $article) {
            if (!$article->isInternal() || $article->isOfficial()) {
                $articles[] = $article;
            }
        }

        return $articles;
    }

    /**
     * @param  Person $person
     * @return array
     */
    private function getCurrentArticleIdsByProf(Person $person)
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findByProf($person);

        $ids = array(0);
        foreach ($subjects as $subject) {
            $ids[] = $subject->getSubject()->getId();
        }

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.removed', 'false'),
                    $query->expr()->in('m.subject', $ids)
                )
            )
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach ($resultSet as $mapping) {
            $edited = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndPreviousIdAndAction('article', $mapping->getArticle()->getId(), 'edit');

            if (isset($edited[0]) && !$edited[0]->isRefused()) {
                $ids[] = $edited[0]->getEntityId();
            } else {
                $ids[] = $mapping->getArticle()->getId();
            }
        }

        return $ids;
    }

    /**
     * @param  Person $person
     * @return array
     */
    private function getAddedArticleIdsByProf(Person $person)
    {
        $added = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllByEntityAndActionAndPerson('article', 'add', $person);

        $ids = array(0);
        foreach ($added as $add) {
            $edited = $this->getEntityManager()
                ->getRepository('CudiBundle\Entity\Prof\Action')
                ->findAllByEntityAndPreviousIdAndAction('article', $add->getEntityId(), 'edit');

            if (isset($edited[0]) && !$edited[0]->isRefused()) {
                $ids[] = $edited[0]->getEntityId();
            } else {
                $ids[] = $add->getEntityId();
            }
        }

        return $ids;
    }

    /**
     * @return array
     */
    private function getRemovedArticleIds()
    {
        $removed = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllByEntityAndAction('article', 'delete');

        $ids = array(0);
        foreach ($removed as $remove) {
            if (!$remove->isRefused()) {
                $ids[] = $remove->getEntityId();
            }
        }

        return $ids;
    }

    /**
     * @param  integer $id
     * @param  Person  $person
     * @return \CudiBundle\Entity\Article|null
     */
    public function findOneByIdAndProf($id, Person $person)
    {
        $subjects = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Subject\ProfMap')
            ->findByProf($person);

        $subjectIds = array(0);
        foreach ($subjects as $subject) {
            $subjectIds[] = $subject->getSubject()->getId();
        }

        $articleIds = array_merge(
            $this->getCurrentArticleIdsByProf($person),
            $this->getAddedArticleIdsByProf($person)
        );

        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('m')
            ->from('CudiBundle\Entity\Article\SubjectMap', 'm')
            ->innerJoin('m.article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.removed', 'false'),
                    $query->expr()->eq('m.article', ':id'),
                    $query->expr()->in('m.subject', $subjectIds),
                    $query->expr()->in('a.id', $articleIds),
                    $query->expr()->notIn('a.id', $this->getRemovedArticleIds())
                )
            )
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($resultSet) {
            if (!$resultSet->getArticle()->isInternal() || $resultSet->getArticle()->isOfficial()) {
                return $resultSet->getArticle();
            }
        }

        $actions = $this->getEntityManager()
            ->getRepository('CudiBundle\Entity\Prof\Action')
            ->findAllByEntityAndEntityIdAndPerson('article', $id, $person);

        if (isset($actions[0])) {
            return $actions[0]->setEntityManager($this->getEntityManager())
                ->getEntity();
        }

        if ($resultSet) {
            return $resultSet->getArticle();
        }
    }
}
