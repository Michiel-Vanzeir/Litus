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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ApiBundle\Repository;

use ApiBundle\Document\Code\Authorization as AuthorizationCode,
    DateTime,
    Doctrine\ODM\MongoDB\DocumentRepository,
    MongoId;

/**
 * Token
 *
 * This class was generated by the Doctrine ODM. Add your own custom
 * repository methods below.
 */
class Token extends DocumentRepository
{
    /**
     * @return array
     */
    public function findAllActiveByAuthorizationCode(AuthorizationCode $authorizationCode)
    {
        $query = $this->createQueryBuilder();
        $resultSet = $query->field('authorizationCode')
            ->equals(new MongoId($authorizationCode->getId()))
            ->field('expirationTime')
            ->gt(new DateTime())
            ->getQuery()
            ->execute()
            ->toArray();

        return $resultSet;
    }
}
