<?php

namespace BrBundle\Repository\Contract;

use BrBundle\Entity\Contract;
use BrBundle\Entity\Product\Order\Entry as OrderEntry;

/**
 * Entry
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Entry extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return integer
     */
    public function findHighestVersionNb()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('MAX(c.version)')
            ->from('BrBundle\Entity\Contract\Entry', 'c')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param  \BrBundle\Entity\Contract $contract
     * @return \Doctrine\ORM\Query
     */
    public function findAllContractEntriesByContract(Contract $contract)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('c')
            ->from('BrBundle\Entity\Contract\Entry', 'c')
            ->where(
                $query->expr()->eq('c.contract', ':contract')
            )
            ->setParameter('contract', $contract)
            ->orderBy('c.version', 'ASC')
            ->addOrderBy('c.position', 'ASC')
            ->getQuery();
    }

    /**
     * @param  \BrBundle\Entity\Contract $contract
     * @param  integer                   $version
     * @return \Doctrine\ORM\Query
     */
    public function findContractEntriesByContractAndVersion(Contract $contract, $version)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('c')
            ->from('BrBundle\Entity\Contract\Entry', 'c')
            ->where(
                $query->expr()->andx(
                    $query->expr()->eq('c.contract', ':contract'),
                    $query->expr()->eq('c.version', ':version')
                )
            )
            ->setParameter('contract', $contract)
            ->setParameter('version', $version)
            ->orderBy('c.position', 'ASC')
            ->getQuery();
    }

    /**
     * @param  OrderEntry $entry
     * @return \Doctrine\ORM\Query
     */
    public function findAllContractEntriesByOrderEntry(OrderEntry $oentry)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('c')
            ->from('BrBundle\Entity\Contract\Entry', 'c')
            ->where(
                $query->expr()->eq('c.orderEntry', ':oentry')
            )
            ->setParameter('oentry', $oentry)
            ->getQuery()
            ->getResult();
    }
}
