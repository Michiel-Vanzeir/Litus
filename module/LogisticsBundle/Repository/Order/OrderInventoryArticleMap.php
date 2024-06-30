<?php

namespace LogisticsBundle\Repository\Order;

use DateTime;
use LogisticsBundle\Entity\InventoryArticle as Article;
use LogisticsBundle\Entity\Order;
use LogisticsBundle\Entity\Order\OrderInventoryArticleMap as InventoryMap;

/**
 * OrderInventoryArticleMap
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderInventoryArticleMap extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    public function findOneByOrderAndArticle(Order $order, Article $article): ?InventoryMap
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('LogisticsBundle\Entity\Order\FlesserkeOrderArticleMap', 'm')
            ->innerJoin('m.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.article', ':article'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                )
            )
            ->setParameter('article', $article)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllOverlappingByOrderAndArticle(Order $order, Article $article): array
    {
        $margin_hours = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('logistics.request_margin_hours');

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('m')
            ->from('LogisticsBundle\Entity\Order\FlesserkeOrderArticleMap', 'm')
            ->innerJoin('m.order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.article', ':article'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->orX(
                        $query->expr()->between(':startDate', 'o.startDate', 'o.endDate'),
                        $query->expr()->between(':endDate', 'o.startDate', 'o.endDate'),
                        $query->expr()->between('o.startDate', ':startDate', ':endDate'),
                        $query->expr()->between('o.endDate', ':startDate', ':endDate'),
                    ),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('article', $article)
            ->setParameter('startDate', $order->getStartDate()->modify('-' . $margin_hours . ' hour'))
            ->setParameter('endDate', $order->getEndDate()->modify('+' . $margin_hours . ' hour'))
            ->setParameter('now', new DateTime())
            ->getQuery()
            ->getResult();
    }
}
