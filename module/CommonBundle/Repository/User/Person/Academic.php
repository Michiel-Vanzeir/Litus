<?php

namespace CommonBundle\Repository\User\Person;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Component\Util\EntityRepository;

/**
 * Academic
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Academic extends \CommonBundle\Repository\User\Person
{
    public function findOneById($id)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->eq('p.id', ':id')
            )
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        return null;
    }

    public function findAllByUsernameQuery($username)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->like('p.username', ':username')
            )
            ->setParameter('username', '%' . strtolower($username) . '%')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByNameQuery($name)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                                $query->expr()->lower('p.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                                $query->expr()->lower('p.firstName')
                            ),
                            ':name'
                        )
                    ),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByUniversityIdentificationQuery($universityIdentification)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->like('p.universityIdentification', ':universityIdentification'),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->setParameter('universityIdentification', '%' . strtolower($universityIdentification) . '%')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByBarcodeQuery($barcode)
    {
        $query = $this->_em->createQueryBuilder();
        $barcodes = $query->select('b')
            ->from('CommonBundle\Entity\User\Barcode', 'b')
            ->where(
                $query->expr()->like($query->expr()->concat('b.barcode', '\'\''), ':barcode')
            )
            ->setParameter('barcode', strtolower($barcode) . '%')
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($barcodes as $barcode)
            $ids[] = $barcode->getPerson()->getId();

        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->in('p.id', $ids),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->getQuery();

        return $resultSet;
    }

    public function findOneByUsername($username)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->eq('p.username', ':username'),
                        $query->expr()->eq('p.universityIdentification', ':username')
                    ),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->setParameter('username', $username)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (isset($resultSet[0]))
            return $resultSet[0];

        $barcode = $this->_em
            ->getRepository('CommonBundle\Entity\User\Barcode')
            ->findOneByBarcode($username);

        if ($barcode)
            return $barcode->getPerson();

        return null;
    }

    public function findAllByNameTypeaheadQuery($name)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('p')
            ->from('CommonBundle\Entity\User\Person\Academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->orX(
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.firstName', "' '")),
                                $query->expr()->lower('p.lastName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like(
                            $query->expr()->concat(
                                $query->expr()->lower($query->expr()->concat('p.lastName', "' '")),
                                $query->expr()->lower('p.firstName')
                            ),
                            ':name'
                        ),
                        $query->expr()->like('p.universityIdentification', ':name')
                    ),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->setMaxResults(20)
            ->getQuery();

        return $resultSet;
    }

    public function findAllMembers(AcademicYear $academicYear)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CommonBundle\Entity\User\Status\Organization', 's')
            ->innerJoin('s.person', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->neq('s.status', '\'non_member\''),
                    $query->expr()->eq('s.academicYear', ':academicYear'),
                    $query->expr()->eq('p.canLogin', 'true')
                )
            )
            ->setParameter('academicYear', $academicYear->getId())
            ->getQuery()
            ->getResult();

        $persons = array();
        foreach($resultSet as $result)
            $persons[] = $result->getPerson();

        return $persons;
    }
}
