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

namespace TicketBundle\Entity;

use CalendarBundle\Entity\Nodes\Event as CalendarEvent,
    DateTime,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="TicketBundle\Repository\Event")
 * @ORM\Table(name="tickets.events")
 */
class Event
{
    /**
     * @var integer The ID of the event
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var string The activity of the event
     *
     * @ORM\OneToOne(targetEntity="CalendarBundle\Entity\Nodes\Event")
     * @ORM\JoinColumn(name="activity", referencedColumnName="id")
     */
    private $activity;

    /**
     * @var boolean Flag whether the tickets are bookable
     *
     * @ORM\Column(type="boolean")
     */
    private $bookable;

    /**
     * @var \DateTime The date the booking system will close
     *
     * @ORM\Column(name="bookings_close_date", type="datetime", nullable=true)
     */
    private $bookingsCloseDate;

    /**
     * @var boolean Flag whether the event booking system is active
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var boolean Flag whether the tickets are generated
     *
     * @ORM\Column(name="tickets_generated", type="boolean")
     */
    private $ticketsGenerated;

    /**
     * @var integer The total number of tickets
     *
     * @ORM\Column(name="number_of_tickets", type="integer", nullable=true)
     */
    private $numberOfTickets;

    /**
     * @var integer The maximum number of tickets bookable by one person
     *
     * @ORM\Column(name="limit_per_person", type="integer", nullable=true)
     */
    private $limitPerPerson;

    /**
     * @var integer Flag whether only members can book tickets
     *
     * @ORM\Column(name="only_members", type="boolean")
     */
    private $onlyMembers;

    /**
     * @param \CalendarBundle\Entity\Nodes\Event $activity
     * @param boolean $bookable
     * @param \DateTime $bookingsCloseDate
     * @param boolean $active
     * @param boolean $ticketsGenerated
     * @param integer $numberOfTickets
     * @param integer $limitPerPerson
     * @param boolean $onlyMembers
     */
    public function __construct(CalendarEvent $activity, $bookable, DateTime $bookingsCloseDate = null, $active, $ticketsGenerated, $numberOfTickets = null, $limitPerPerson = null, $onlyMembers)
    {
        $this->activity = $activity;
        $this->bookable = $bookable;
        $this->bookingsCloseDate = $bookingsCloseDate;
        $this->active = $active;
        $this->ticketsGenerated = $ticketsGenerated;
        $this->numberOfTickets = $numberOfTickets;
        $this->limitPerPerson = $limitPerPerson;
        $this->onlyMembers = $onlyMembers;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CalendarBundle\Entity\Nodes\Event
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param \CalendarBundle\Entity\Nodes\Event $activity
     * @return \TicketBunlde\Entity\Event
     */
    public function setActivity(CalendarEvent $activity)
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isBookable()
    {
        return $this->bookable;
    }

    /**
     * @param boolean $bookable
     * @return \TicketBunlde\Entity\Event
     */
    public function setBookable($bookable)
    {
        $this->bookable = $bookable;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBookingsCloseDate()
    {
        return $this->bookingsCloseDate;
    }

    /**
     * @param \DateTime $bookingsCloseDate
     * @return \TicketBunlde\Entity\Event
     */
    public function setBookingsCloseDate(DateTime $bookingsCloseDate)
    {
        $this->bookingsCloseDate = $bookingsCloseDate;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return \TicketBunlde\Entity\Event
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return boolean
     */
    public function areTicketsGenerated()
    {
        return $this->ticketsGenerated;
    }

    /**
     * @param boolean $ticketsGenerated
     * @return \TicketBunlde\Entity\Event
     */
    public function setTicketsGenerated($ticketsGenerated)
    {
        $this->ticketsGenerated = $ticketsGenerated;
        return $this;
    }

    /**
     * @return integer
     */
    public function getNumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * @param integer $numberOfTickets
     * @return \TicketBunlde\Entity\Event
     */
    public function setNumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;
        return $this;
    }

    /**
     * @return integer
     */
    public function getLimitPerPerson()
    {
        return $this->limitPerPerson;
    }

    /**
     * @param integer $limitPerPerson
     * @return \TicketBunlde\Entity\Event
     */
    public function setLimitPerPerson($limitPerPerson)
    {
        $this->limitPerPerson = $limitPerPerson;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOnlyMembers()
    {
        return $this->onlyMembers;
    }

    /**
     * @param boolean $onlyMembers
     * @return \TicketBunlde\Entity\Event
     */
    public function setOnlyMembers($onlyMembers)
    {
        $this->onlyMembers = $onlyMembers;
        return $this;
    }
}