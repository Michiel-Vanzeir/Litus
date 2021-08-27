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

namespace PageBundle\Form\Admin\Page;

use PageBundle\Entity\Node\Page as PageEntity;

/**
 * Add FAQ
 */
class FAQ extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'PageBundle\Hydrator\Node\Page';

    /**
     * @var PageEntity
     */
    private $page;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'faq_typeahead',
                'label'    => 'FAQ Name',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'FAQ',
                                'options' => array(
                                    'page' => $this->getPage(),
                                ),
                            )
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'faq_add',
                'value'      => 'Add',
                'attributes' => array(
                    'class' => 'faq_add',
                ),
            )
        );

        if ($this->getPage() !== null) {
            $this->bind($this->getPage());
        }
    }

    /**
     * @param PageEntity $page
     * @return self
     */
    public function setPage(PageEntity $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return PageEntity
     */
    public function getPage()
    {
        return $this->page;
    }
}