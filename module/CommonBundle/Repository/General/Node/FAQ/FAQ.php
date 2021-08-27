<?php

namespace CommonBundle\Repository\General\Node\FAQ;

use PageBundle\Entity\Node\Page;

/**
 * FAQ
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FAQ extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{

    /**
     * @param Page $page
     * @return \Doctrine\ORM\Query
     */
    public function findAllByPageQuery(Page $page)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('f')
            ->from('CommonBundle\Entity\General\Node\FAQ\FAQ', 'f')
            ->innerJoin('f.pages', 'p')
            ->where(
                $query->expr()->eq(':id', 'p.id'))
            ->orderBy('f.name', 'ASC')
            ->setParameter('id', $page->getId())
            ->getQuery();
    }

    /**
     * @param string $name
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameQuery(string $name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('f')
            ->from('CommonBundle\Entity\General\Node\FAQ\FAQ', 'f')
            ->where(
                $query->expr()->like($query->expr()->lower('f.name'), ':name')
            )
            ->orderBy('f.name', 'ASC')
            ->setParameter('name', '%'.strtolower($name).'%')
            ->getQuery();
    }
}
