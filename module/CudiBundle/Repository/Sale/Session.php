<?php

namespace CudiBundle\Repository\Sale;

use CommonBundle\Component\Util\AcademicYear as AcademicYearUtil,
    CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\General\Organization,
    CudiBundle\Entity\Sale\Session as SessionEntity,
    DateTime,
    CommonBundle\Component\Doctrine\ORM\EntityRepository;

/**
 * Session
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Session extends EntityRepository
{
    public function findOneByCashRegister(CashRegister $cashRegister)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Sale\Session', 's')
            ->where(
                $query->expr()->orX(
                    $query->expr()->eq('s.openRegister', ':register'),
                    $query->expr()->eq('s.closeRegister', ':register')
                )
            )
            ->setParameter('register', $cashRegister->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    private function _personsByAcademicYearAndOrganization(AcademicYear $academicYear, Organization $organization = null)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        $resultSet = $query->select('p.id')
            ->from('CommonBundle\Entity\User\Person\Organization\AcademicYearMap', 'm')
            ->innerJoin('m.academic', 'p')
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('m.organization', ':organization'),
                    $query->expr()->eq('m.academicYear', ':academicYear')
                )
            )
            ->setParameter('organization', $organization)
            ->setParameter('academicYear', $academicYear)
            ->getQuery()
            ->getResult();

        $ids = array(0);
        foreach($resultSet as $item) {
            $ids[] = $item['id'];
        }

        return $ids;
    }

    public function getTheoreticalRevenue(SessionEntity $session, Organization $organization = null)
    {
        if ($organization !== null) {
            $session->setEntityManager($this->getEntityManager());

            $ids = $this->_personsByAcademicYearAndOrganization($session->getAcademicYear(), $organization);

            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.price)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.queueItem', 'q')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->in('q.person', $ids),
                        $query->expr()->eq('s.session', ':session')
                    )
                )
                ->setParameter('session', $session->getId())
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.price)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->where(
                    $query->expr()->eq('s.session', ':session')
                )
                ->setParameter('session', $session->getId())
                ->getQuery()
                ->getSingleScalarResult();
        }

        if (null === $resultSet)
            $resultSet = 0;

        return $resultSet;
    }

    public function getTheoreticalRevenueByAcademicYear(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->getTheoreticalRevenueBetween($academicYear->getUniversityStartDate(), $academicYear->getUniversityEndDate(), $organization);
    }

    public function getTheoreticalRevenueBetween(DateTime $startDate, DateTime $endDate, Organization $organization = null)
    {
        if ($organization !== null) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByUniversityStart(AcademicYearUtil::getStartOfAcademicYear($startDate));

            $ids = $this->_personsByAcademicYearAndOrganization($academicYear, $organization);

            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.price)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.session', 'e')
                ->innerJoin('s.queueItem', 'q')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->in('q.person', $ids),
                        $query->expr()->gt('e.openDate', ':start'),
                        $query->expr()->lt('e.openDate', ':end')
                    )
                )
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.price)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.session', 'e')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->gt('e.openDate', ':start'),
                        $query->expr()->lt('e.openDate', ':end')
                    )
                )
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getSingleScalarResult();
        }

        if (null === $resultSet)
            $resultSet = 0;

        return $resultSet;
    }

    public function getPurchasedAmountBySession(SessionEntity $session, Organization $organization = null)
    {
        if ($organization !== null) {
            $session->setEntityManager($this->getEntityManager());

            $ids = $this->_personsByAcademicYearAndOrganization($session->getAcademicYear(), $organization);

            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.purchasePrice)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.queueItem', 'q')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->in('q.person', $ids),
                        $query->expr()->eq('s.session', ':session')
                    )
                )
                ->setParameter('session', $session->getId())
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.purchasePrice)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->where(
                    $query->expr()->eq('s.session', ':session')
                )
                ->setParameter('session', $session->getId())
                ->getQuery()
                ->getSingleScalarResult();
        }

        if (null === $resultSet)
            $resultSet = 0;

        return $resultSet;
    }

    public function getPurchasedAmountByAcademicYear(AcademicYear $academicYear, Organization $organization = null)
    {
        return $this->getPurchasedAmountBetween($academicYear->getUniversityStartDate(), $academicYear->getUniversityEndDate(), $organization);
    }

    public function getPurchasedAmountBetween(DateTime $startDate, DateTime $endDate, Organization $organization = null)
    {
        if ($organization !== null) {
            $academicYear = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\AcademicYear')
                ->findOneByUniversityStart(AcademicYearUtil::getStartOfAcademicYear($startDate));

            $ids = $this->_personsByAcademicYearAndOrganization($academicYear, $organization);

            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.purchasePrice)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.session', 'e')
                ->innerJoin('s.queueItem', 'q')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->in('q.person', $ids),
                        $query->expr()->gt('e.openDate', ':start'),
                        $query->expr()->lt('e.openDate', ':end')
                    )
                )
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getSingleScalarResult();
        } else {
            $query = $this->_em->createQueryBuilder();
            $resultSet = $query->select('SUM(s.purchasePrice)')
                ->from('CudiBundle\Entity\Sale\SaleItem', 's')
                ->innerJoin('s.session', 'e')
                ->where(
                    $query->expr()->andX(
                        $query->expr()->gt('e.openDate', ':start'),
                        $query->expr()->lt('e.openDate', ':end')
                    )
                )
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getSingleScalarResult();
        }

        if (null === $resultSet)
            $resultSet = 0;

        return $resultSet;
    }

    public function getLast()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Sale\Session', 's')
            ->orderBy('s.openDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOnebyDate(DateTime $date)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Sale\Session', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->lte('s.openDate', ':now'),
                    $query->expr()->gte('s.closeDate', ':now')
                )
            )
            ->setParameter('now', $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $resultSet;
    }

    public function findOpenQuery()
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Sale\Session', 's')
            ->where(
                $query->expr()->isNull('s.closeDate')
            )
            ->orderBy('s.openDate', 'DESC')
            ->getQuery();

        return $resultSet;
    }

    public function findAllByAcademicYearQuery(AcademicYear $academicYear)
    {
        return $this->findAllBetweenQuery($academicYear->getUniversityStartDate(), $academicYear->getUniversityEndDate());
    }

    public function findAllBetweenQuery(DateTime $startDate, DateTime $endDate)
    {
        $query = $this->_em->createQueryBuilder();
        $resultSet = $query->select('s')
            ->from('CudiBundle\Entity\Sale\Session', 's')
            ->where(
                $query->expr()->andX(
                    $query->expr()->gt('s.openDate', ':start'),
                    $query->expr()->lt('s.openDate', ':end')
                )
            )
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery();

        return $resultSet;
    }
}
