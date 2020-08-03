<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

namespace SyllabusBundle\Repository;

/**
 * Subject
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Subject extends \CommonBundle\Component\Doctrine\ORM\EntityRepository
{
    /**
     * @return \Doctrine\ORM\Query
     */
    public function findAllQuery()
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->orderBy('s.name')
            ->getQuery();
    }

    /**
     * @param  string $name
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameQuery($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->like($query->expr()->lower('s.name'), ':name')
            )
            ->setParameter('name', '%' . strtolower($name) . '%')
            ->orderBy('s.name')
            ->getQuery();
    }

    /**
     * @param  string $nameOrCode
     * @return \Doctrine\ORM\Query
     */
    public function findAllByNameOrCodeQuery($nameOrCode)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->orX($query->expr()->like($query->expr()->lower('s.name'), ':name'),
                                    $query->expr()->like($query->expr()->lower('s.code'), ':name')
            )
            )
            ->setParameter('name', '%' . strtolower($nameOrCode) . '%')
            ->orderBy('s.name')
            ->getQuery();
    }

    /**
     * @param  string $code
     * @return \Doctrine\ORM\Query
     */
    public function findAllByCodeQuery($code)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->where(
                $query->expr()->like($query->expr()->lower('s.code'), ':code')
            )
            ->setParameter('code', '%' . strtolower($code) . '%')
            ->orderBy('s.code')
            ->getQuery();
    }

    /**
     * @param  string $prof
     * @return \Doctrine\ORM\Query
     */
    public function findAllByProfQuery($prof)
    {
        $query = $this->getEntityManager()->createQueryBuilder();
        return $query->select('s')
            ->from('SyllabusBundle\Entity\Subject', 's')
            ->leftJoin(
                'SyllabusBundle\Entity\Subject\ProfMap',
                'm',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'm.subject = s.id'
            )
            ->innerJoin('m.prof', 'p')
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
            ->setParameter('name', '%' . strtolower($prof) . '%')
            ->orderBy('s.code')
            ->getQuery();
    }
}
