<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Sales\Session;

use CommonBundle\Entity\General\Bank\CashRegister,
    CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    Doctrine\ORM\EntityManager,
    Zend\Form\Element\Submit;

/**
 * Close Sale Session
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Close extends Add
{
    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param \CommonBundle\Entity\General\Bank\CashRegister $cashRegister
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, CashRegister $cashRegister, $name = null )
    {
        parent::__construct($entityManager, $name);

        $this->remove('submit');

        $field = new Submit('submit');
        $field->setValue('Close')
            ->setAttribute('class', 'sale_edit');
        $this->add($field);

        $this->populateFromCashRegister($cashRegister);
    }
}
