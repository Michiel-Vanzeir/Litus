<?php

namespace CommonBundle\Repository\General\Organization;

use Doctrine\ORM\EntityRepository;

/**
 * Unit
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Unit extends EntityRepository
{
    public function findAllActive()
    {
        return $this->_em->getRepository('CommonBundle\Entity\General\Organization\Unit')
            ->findBy(array('active' => true), array('name' => 'ASC'));
    }
}