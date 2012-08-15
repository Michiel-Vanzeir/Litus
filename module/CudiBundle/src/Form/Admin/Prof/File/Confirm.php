<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace CudiBundle\Form\Admin\Prof\File;

use CommonBundle\Component\Form\Admin\Decorator\ButtonDecorator,
    CommonBundle\Component\Form\Admin\Decorator\FieldDecorator,
    CudiBundle\Entity\Files\Mapping as FileMapping,
    Zend\Form\Element\Checkbox,
    Zend\Form\Element\Submit,
    Zend\Form\Element\Text;

/**
 * Confirm File add action
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Confirm extends \CommonBundle\Component\Form\Admin\Form
{
    public function __construct(FileMapping $mapping, $options = null)
    {
        parent::__construct($options);

        $this->setAttrib('id', 'uploadFile');

        $field = new Text('description');
        $field->setLabel('Description')
            ->setAttrib('size', 70)
            ->setRequired()
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Checkbox('printable');
        $field->setLabel('Printable')
            ->setDecorators(array(new FieldDecorator()));
        $this->addElement($field);

        $field = new Submit('submit');
        $field->setLabel('Confirm')
                ->setAttrib('class', 'file_add')
                ->setDecorators(array(new ButtonDecorator()));
        $this->addElement($field);

        $this->populateFromFile($mapping);
    }

    public function populateFromFile(FileMapping $mapping)
    {
        $this->populate(
            array(
            'description' => $mapping->getFile()->getDescription()
            )
        );
    }
}
