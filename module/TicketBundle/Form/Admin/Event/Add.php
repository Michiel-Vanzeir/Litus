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

namespace TicketBundle\Form\Admin\Event;

use CommonBundle\Component\Form\Admin\Element\Select,
    CommonBundle\Component\Form\Admin\Element\Text,
    CommonBundle\Component\Form\Admin\Element\Checkbox,
    CommonBundle\Component\Validator\DateCompare as DateCompareValidator,
    CommonBundle\Component\Validator\Price as PriceValidator,
    Doctrine\ORM\EntityManager,
    Ticketbundle\Entity\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Add Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Admin\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;

        $field = new Select('event');
        $field->setLabel('Event')
            ->setAttribute('options', $this->_createEventsArray());
        $this->add($field);

        $field = new Checkbox('active');
        $field->setLabel('Active');
        $this->add($field);

        $field = new Checkbox('bookable');
        $field->setLabel('Bookable');
        $this->add($field);

        $field = new Text('bookings_close_date');
        $field->setLabel('Booking Close Date')
            ->setAttribute('placeholder', 'dd/mm/yyyy hh:mm')
            ->setAttribute('data-datepicker', true)
            ->setAttribute('data-timepicker', true);
        $this->add($field);

        $field = new Checkbox('generate_tickets');
        $field->setLabel('Generate Tickets');
        $this->add($field);

        $field = new Text('number_of_tickets');
        $field->setLabel('Number Of Tickets (0 is infinity)')
            ->setValue(0);
        $this->add($field);

        $field = new Text('limit_per_person');
        $field->setLabel('Maximum Number Of Tickets Per Person  (0 is infinity)')
            ->setValue(0);
        $this->add($field);

        $field = new Checkbox('only_members');
        $field->setLabel('Only Members');
        $this->add($field);

        $field = new Text('price_members');
        $field->setLabel('Price Members');
        $this->add($field);

        $field = new Text('price_non_members');
        $field->setLabel('Price Non Members');
        $this->add($field);

        $field = new Submit('submit');
        $field->setValue('Add')
            ->setAttribute('class', 'shift_add');
        $this->add($field);
    }

    public function populateFromEvent(Event $event)
    {
        $this->setData(
            array(
                'event' => $event->getActivity()->getId(),
                'active' => $event->isActive(),
                'bookable' => $event->isBookable(),
                'bookings_close_date' => $event->getBookingsCloseDate() ? $event->getBookingsCloseDate()->format('d/m/Y H:i') : '',
                'generate_tickets' => $event->areTicketsGenerated(),
                'number_of_tickets' => $event->getNumberOfTickets(),
                'limit_per_person' => $event->getLimitPerPerson(),
                'only_members' => $event->isOnlyMembers(),
                'price_members' => number_format($event->getPriceMembers()/100, 2),
                'price_non_members' => number_format($event->getPriceNonMembers()/100, 2),
            )
        );
    }

    private function _createEventsArray()
    {
        $events = $this->_entityManager
            ->getRepository('CalendarBundle\Entity\Nodes\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => ''
        );
        foreach ($events as $event)
            $eventsArray[$event->getId()] = $event->getTitle();

        return $eventsArray;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'event',
                    'required' => true,
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'bookings_close_date',
                    'required' => false,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'date',
                            'options' => array(
                                'format' => 'd/m/Y H:i',
                            ),
                        ),
                        new DateCompareValidator('now', 'd/m/Y H:i'),
                    ),
                )
            )
        );

        if (isset($this->data['generate_tickets']) && $this->data['generate_tickets']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'number_of_tickets',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                            array(
                                'name' => 'greaterthan',
                                'options' => array(
                                    'min' => 0,
                                )
                            )
                        ),
                    )
                )
            );
        } else {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'number_of_tickets',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                        ),
                    )
                )
            );
        }

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'limit_per_person',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array('name' => 'int'),
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'price_members',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator()
                    ),
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'price_non_members',
                    'required' => true,
                    'filters'  => array(
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        new PriceValidator()
                    ),
                )
            )
        );

        return $inputFilter;
    }
}
