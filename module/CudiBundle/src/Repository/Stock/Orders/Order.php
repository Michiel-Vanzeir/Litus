<?php

namespace CudiBundle\Repository\Stock\Orders;

use CommonBundle\Entity\Users\Person,
    CudiBundle\Entity\Sales\Article,
    CudiBundle\Entity\Stock\Orders\Item as ItemEntity,
    CudiBundle\Entity\Stock\Orders\Order as OrderEntity,
    CudiBundle\Entity\Stock\Period,
    CudiBundle\Entity\Supplier,
    Doctrine\ORM\EntityRepository;

/**
 * Order
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Order extends EntityRepository
{
    public function findAllBySupplierAndPeriod(Supplier $supplier, Period $period)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('o')
            ->from('CudiBundle\Entity\Stock\Orders\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.supplier', ':supplier'),
                    $query->expr()->gt('o.dateCreated', ':startDate'),
                    $period->isOpen() ? '1=1' : $query->expr()->lt('o.dateCreated', ':endDate')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setParameter('startDate', $period->getStartDate())
            ->orderBy('o.dateCreated', 'DESC');

        if (!$period->isOpen())
            $query->setParameter('endDate', $period->getEndDate());

        $resultSet = $query->getQuery()
            ->getResult();

        return $resultSet;
    }

    public function findOneOpenBySupplier(Supplier $supplier)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('o')
            ->from('CudiBundle\Entity\Stock\Orders\Order', 'o')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('o.supplier', ':supplier'),
                    $query->expr()->isNull('o.dateOrdered')
                )
            )
            ->setParameter('supplier', $supplier->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function addNumberByArticle(Article $article, $number, Person $person)
    {
        $item = $this->_em
            ->getRepository('CudiBundle\Entity\Stock\Orders\Item')
            ->findOneOpenByArticle($article);

        if (isset($item)) {
            $item->setNumber($item->getNumber() + $number);
        } else {
            $order = $this->findOneOpenBySupplier($article->getSupplier());
            if (null === $order) {
                $order = new OrderEntity($article->getSupplier(), $person);
                $this->_em->persist($order);
            }

            $item = new ItemEntity($article, $order, $number);
            $this->_em->persist($item);
        }

        return $item;
    }
}
