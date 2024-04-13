<?php

namespace LogisticsBundle\Repository;

use CommonBundle\Entity\General\Config;
use CommonBundle\Entity\General\Organization\Unit;
use CommonBundle\Entity\User\Person\Academic;
use DateTime;
use LogisticsBundle\Entity\Order as OrderEntity;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * The standard finds only look for active and new orders
 */
class Order extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function findAll(): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findAllOld(): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->lt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $name
     * @return array
     */
    public function findAllByName(string $name): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.name', ':name'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  Academic $creator
     * @return array
     */
    public function findAllByCreator(Academic $creator): array
    {

        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.creator', ':creator'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('creator', $creator)
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Unit $unit
     * @return array
     */
    public function findAllByUnit(Unit $unit): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->join('o.units', 'u')  // TODO: check if this works
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('u', ':unit'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('unit', $unit)
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $status
     * @return array
     */
    public function findAllByStatus(string $status): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.status', ':status'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $transport
     * @return array
     */
    public function findAllByTransport(string $transport): array
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('o.transport', ':transport'),
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->setParameter('transport', $transport)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param OrderEntity $order
     * @return array
     */
    public function findAllOverlappingByOrder(OrderEntity $order): array
    {
        $margin_hours = $this->getEntityManager()
            ->getRepository(Config::class)
            ->getConfigValue('logistics.request_margin_hours');
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('o')
            ->from(OrderEntity::class, 'o')
            ->where(
                $query->expr()->andx(
                    $query->expr()->gt('o.endDate', ':now'),
                    $query->expr()->eq('o.active', 'TRUE'),
                    $query->expr()->neq('o.status', ':status'),
                    $query->expr()->orX(
                        $query->expr()->between(':startDate', 'o.startDate', 'o.endDate'),
                        $query->expr()->between(':endDate', 'o.startDate', 'o.endDate'),
                        $query->expr()->between('o.startDate', ':startDate', ':endDate'),
                        $query->expr()->between('o.endDate', ':startDate', ':endDate'),
                    ),
                )
            )
            ->orderBy('o.startDate', 'ASC')
            ->setParameter('startDate', $order->getStartDate()->modify('-' . $margin_hours . ' hour'))
            ->setParameter('endDate', $order->getEndDate()->modify('+' . $margin_hours . ' hour'))
            ->setParameter('now', new DateTime())
            ->setParameter('status', 'Removed')
            ->getQuery()
            ->getResult();
    }
}
