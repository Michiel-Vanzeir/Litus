<?php

namespace BrBundle\Repository;

/**
 * Invoice
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Invoice extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllUnPayedQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->isNull('i.paidTime')
            )
            ->orderBy('i.creationTime', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string $invoiceYear The year from which you want to find all the unpayed invoices.
     * @return \Doctrine\ORM\Query
     */
    public function findAllUnPayedByInvoiceYearQuery($invoiceYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->andX(
                    $query->expr()->isNull('i.paidTime'),
                    $query->expr()->like('i.invoiceNumberPrefix', ':invoiceYear')
                )
            )
            ->setParameter('invoiceYear', $invoiceYear . '%')
            ->orderBy('i.creationTime', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string $invoiceYear The year from which you want to find all the invoices.
     * @return \Doctrine\ORM\Query
     */
    public function findAllByInvoiceYearQuery($invoiceYear)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->like('i.invoiceNumberPrefix', ':invoiceYear')
            )
            ->setParameter('invoiceYear', $invoiceYear . '%')
            ->orderBy('i.creationTime', 'DESC')
            ->getQuery();
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllPayedQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->isNotNull('i.paidTime')
            )
            ->orderBy('i.creationTime', 'DESC')
            ->getQuery();
    }

    /**
     * @param  string $invoicePrefix The invoice prefix for which you want to find the next invoice number
     * @return integer
     */
    public function findNextInvoiceNb($invoicePrefix)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $highestInvoiceNb = $query->select('COALESCE(MAX(i.invoiceNb), 0)')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->where(
                $query->expr()->eq('i.invoiceNumberPrefix', ':prefix')
            )
            ->setParameter('prefix', $invoicePrefix)
            ->getQuery()
            ->getSingleScalarResult();

        return $highestInvoiceNb + 1;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllInvoicePrefixes()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('i.invoiceNumberPrefix')
            ->from('BrBundle\Entity\Invoice', 'i')
            ->distinct()
            ->getQuery()
            ->getResult();
    }
}
