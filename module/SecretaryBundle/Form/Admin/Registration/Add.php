<?php

namespace SecretaryBundle\Form\Admin\Registration;

/**
 * Add Registration
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'typeahead',
                'name'     => 'person',
                'label'    => 'Person',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'validators' => array(
                            array('name' => 'TypeaheadPerson'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'checkbox',
                'name'     => 'payed',
                'label'    => 'Has Payed',
                'required' => true,
            )
        );

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'organization',
                'label'      => 'Organization',
                'required'   => true,
                'attributes' => array(
                    'options' => $this->getOrganizations(),
                ),
            )
        );

        $this->addSubmit('Save', 'secretary_edit');
    }

    private function getOrganizations()
    {
        $organizations = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Organization')
            ->findAll();

        $organizationOptions = array();
        foreach ($organizations as $organization) {
            $organizationOptions[$organization->getId()] = $organization->getName();
        }

        return $organizationOptions;
    }
}
