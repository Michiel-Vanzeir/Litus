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
namespace LogisticsBundle\Entity;

use CommonBundle\Entity\Users\Person,
    CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a driver.
 *
 * @ORM\Entity(repositoryClass="LogisticsBundle\Repository\Driver")
 * @ORM\Table(name="logistics.driver")
 */
class Driver
{

    /**
     * @var \CommonBundle\Entity\Users\Person The person this driver represents
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Users\People\Academic", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @ORM\ManyToMany(targetEntity="CommonBundle\Entity\General\AcademicYear", cascade={"persist"})
     * @ORM\JoinTable(name="logistics.driver_years",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="person")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="year_id", referencedColumnName="id")}
     * )
     */
    private $years;

    /**
     * @var string The color for used for this driver.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $color;

    /**
     * Creates a new driver for the given person
     *
     * @param \CommonBundle\Entity\Users\Person $person The person to mark as a driver.
     * @param array $years The years in which this person was a driver.
     */
    public function __construct(Person $person, $color)
    {
        $this->person = $person;
        $this->color = $color;
        $this->years = new ArrayCollection();
    }

    /**
     * @return \CommonBundle\Entity\Users\Person The person associated with this driver.
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Retrieves the years in which this person was a driver.
     *
     * @return array The years in which this person was a driver.
     */
    public function getYears() {
        return $this->years->toArray();
    }

    /**
     * @param array $years Sets the years in which this person was a driver.
     * @return \LogisticsBundle\Entity\Driver This
     */
    public function setYears(array $years) {
        $this->years = new ArrayCollection($years);
        return $this;
    }

    /**
     * Retrieves the color used to display the reservations assigned to this driver in the calendar.
     *
     * @return string The color code for this driver.
     */
    public function getColor() {
        if ($this->color)
            return $this->color;
        else
            return '#888888';
    }

    /**
     * @param string $color Sets the color used for this driver.
     * @return \LogisticsBundle\Entity\Driver This
     */
    public function setColor($color) {
        $this->color = $color;
        return $this;
    }

}