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

namespace MailBundle\Form\Admin\Study;


use CommonBundle\Entity\General\AcademicYear,
    MailBundle\Component\Validator\MultiMail as MultiMailValidator;

/**
 * Send Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    const FILESIZE = '50MB';

    /**
     * @var AcademicYear
     */
    private $_academicYear;

    public function init()
    {
        parent::init();

        $this->setAttribute('id', 'uploadFile');

        $studies = $this->_getStudies();
        if (0 != count($studies)) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'studies',
                'label'      => 'Studies',
                'attributes' => array(
                    'style' => 'max-width: 400px;',
                    'multiple' => true,
                ),
                'options'    => array(
                    'options' => $studies,
                ),
            ));
        }

        $groups = $this->_getGroups();
        if (0 != count($groups)) {
            $this->add(array(
                'type'       => 'select',
                'name'       => 'groups',
                'label'      => 'Groups',
                'attributes' => array(
                    'multiple' => true,
                ),
                'options'    => array(
                    'options' => $groups,
                ),
            ));
        }

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'test',
            'label'      => 'Test Mail',
        ));

        $this->add(array(
            'type'       => 'checkbox',
            'name'       => 'html',
            'label'      => 'HTML Mail',
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'from',
            'label'      => 'From',
            'required'   => true,
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'emailAddress'),
                    ),
                ),
            ),
        ));

        $this->add(array(
            'type'       => 'text',
            'name'       => 'bcc',
            'label'      => 'Additional BCC',
            'attributes' => array(
                'style' => 'width: 400px;',
            ),
            'options'    => array(
                'input' => array(
                    'filters' => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new MultiMailValidator(),
                    ),
                ),
            ),
        ));

        $storedMessages = $this->_getStoredMessages();
        if (1 < count($storedMessages)) {
            $this->add(array(
                'type'     => 'fieldset',
                'name'     => 'select_message',
                'label'    => 'Select Message',
                'elements' => array(
                    array(
                        'type'       => 'select',
                        'name'       => 'stored_message',
                        'label'      => 'Stored Message',
                        'attributes' => array(
                            'style' => 'max-width: 100%;',
                        ),
                        'options'    => array(
                            'options' => $storedMessages,
                        ),
                    ),
                ),
            ));
        }

        $this->add(array(
            'type'     => 'fieldset',
            'name'     => 'compose_message',
            'label'    => 'Compose Message',
            'elements' => array(
                array(
                    'type'       => 'text',
                    'name'       => 'subject',
                    'label'      => 'Subject',
                    'required'   => true,
                    'attributes' => array(
                        'style' => 'width: 400px;',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'textarea',
                    'name'       => 'message',
                    'label'      => 'Message',
                    'required'   => true,
                    'attributes' => array(
                        'style' => 'width: 500px; height: 200px;',
                    ),
                    'options'    => array(
                        'input' => array(
                            'filters' => array(
                                array('name' => 'StringTrim'),
                            ),
                        ),
                    ),
                ),
                array(
                    'type'       => 'file',
                    'name'       => 'file',
                    'label'      => 'Attachments',
                    'attributes' => array(
                        'multiple' => true,
                        'data-help' => 'The maximum file size is ' . self::FILESIZE . '.',
                    ),
                    'options'    => array(
                        'input' => array(
                            'validators' => array(
                                array(
                                    'name' => 'filesize',
                                    'options' => array(
                                        'max' => self::FILESIZE,
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addSubmit('Send', 'mail', 'Send');
    }

    /**
     * @param  AcademicYear $academicYear
     * @return self
     */
    public function setAcademicYear(AcademicYear $academicYear)
    {
        $this->_academicYear = $academicYear;

        return $this;
    }

    private function _getStudies()
    {
        $studies = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Study')
            ->findAllParentsByAcademicYear($this->_academicYear);

        $studyNames = array();
        foreach ($studies as $study) {
            $studyNames[$study->getId()] = 'Phase ' . $study->getPhase() . ' - ' . $study->getFullTitle();
        }

        return $studyNames;
    }

    private function _getGroups()
    {
        $groups = $this->getEntityManager()
            ->getRepository('SyllabusBundle\Entity\Group')
            ->findAll();

        $groupNames = array();
        foreach ($groups as $group) {
            $groupNames[$group->getId()] = $group->getName();
        }

        return $groupNames;
    }

    private function _getStoredMessages()
    {
        $storedMessages = $this->getDocumentManager()
            ->getRepository('MailBundle\Document\Message')
            ->findAll();

        $storedMessagesTitles = array(
            '' => '',
        );
        foreach ($storedMessages as $storedMessage) {
            $storedMessagesTitles[$storedMessage->getId()] = '(' . $storedMessage->getCreationTime()->format('d/m/Y') . ') ' . $storedMessage->getSubject();
        }

        return $storedMessagesTitles;
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        if ($this->has('select_message') && $this->get('select_message')->get('stored_message')->getValue() != '') {
            $specs['compose_message']['subject']['required'] = false;
            $specs['compose_message']['message']['required'] = false;
        }

        return $specs;
    }
}
