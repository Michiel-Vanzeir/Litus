<?php

namespace LogisticsBundle\Repository;

use Doctrine\ORM\Query;

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
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->innerJoin('a.unit', 'u')
            ->orderBy('u.name', 'ASC')
            ->addOrderBy('a.category', 'ASC')
            ->addOrderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $name
     * @return Query
     */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $visibility
     * @return Query
     */
    public function findAllByVisibilityQuery($visibility)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.visibility'), ':visibility')
            )
            ->setParameter('visibility', '%' . strtolower($visibility) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $visibility
     * @return Query
     */
    public function findAllByUnitNameQuery($unitName)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->innerJoin('a.unit', 'u')
            ->where(
                $query->expr()->like($query->expr()->lower('u.name'), ':unitName')
            )
            ->setParameter('unitName', '%' . strtolower($unitName) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $status
     * @return Query
     */
    public function findAllByStatusQuery($status)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.status'), ':status')
            )
            ->setParameter('status', '%' . strtolower($status) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param  string $location
     * @return Query
     */
    public function findAllByLocationQuery($location)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->like($query->expr()->lower('a.location'), ':location')
            )
            ->setParameter('location', '%' . strtolower($location) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @param string $type
     * @param string $location
     * @return Query
     */
    public function findAllByTypeAndLocationQuery($type, $location)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('a')
            ->from('LogisticsBundle\Entity\Article', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like($query->expr()->lower('a.location'), ':location'),
                    $query->expr()->like($query->expr()->lower('a.category'), ':type'),
                )
            )
            ->setParameter('location', '%' . strtolower($location) . '%')
            ->setParameter('type', '%' . strtolower($type) . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }
}
