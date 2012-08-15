<?php

namespace GalleryBundle\Repository\Album;

use DateTime,
    Doctrine\ORM\EntityRepository;

/**
 * Album
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Album extends EntityRepository
{
    public function findAll()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('GalleryBundle\Entity\Album\Album', 'a')
            ->orderBy('a.dateActivity', 'ASC')
            ->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findAllFromTo(DateTime $start, DateTime $end)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('a')
            ->from('GalleryBundle\Entity\Album\Album', 'a')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gte('a.dateActivity', ':start'),
                    $query->expr()->lt('a.dateActivity', ':end')
                )
            )
            ->orderBy('a.dateActivity', 'ASC')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();

        return $resultSet;
    }
}
