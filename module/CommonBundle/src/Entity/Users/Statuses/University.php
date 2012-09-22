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

namespace CommonBundle\Entity\Users\Statuses;

use CommonBundle\Entity\General\AcademicYear,
    CommonBundle\Entity\Users\People\Academic,
    Doctrine\ORM\Mapping as ORM;

/**
 * A classification of a user based on his status at our Alma Mater.
 *
 * @ORM\Entity(repositoryClass="CommonBundle\Repository\Users\Statuses\University")
 * @ORM\Table(name="users.university_statuses")
 */
class University
{
    /**
     * @static
     * @var array All the possible status values allowed
     */
    public static $possibleStatuses = array(
        'alumnus'                  => 'Alumnus',
        'assistant_professor'      => 'Assistant Professor',
        'administrative_assistant' => 'Administrative Assistant',
        'external_student'         => 'External Student',
        'professor'                => 'Professor',
        'student'                  => 'Student',
    );

    /**
     * @var int The ID of this university status
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\Users\People\Academic The person this university status belongs to
     *
     * @ORM\ManyToOne(
     *      targetEntity="CommonBundle\Entity\Users\People\Academic", inversedBy="universityStatuses"
     * )
     * @ORM\JoinColumn(name="person", referencedColumnName="id")
     */
    private $person;

    /**
     * @var string The actual status value
     *
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the status
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * @param \CommonBundle\Entity\Users\People\Academic $person The person that should be given the status
     * @param string $status The status that should be given to the person
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the status
     * @throws \InvalidArgumentException
     */
    public function __construct(Academic $person, $status, AcademicYear $academicYear)
    {
        if (!self::isValidPerson($person, $academicYear))
            throw new \InvalidArgumentException('Invalid person');

        $this->person = $person;

        $this->setStatus($status);
        $this->academicYear = $academicYear;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\Users\People\Academic
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Returns whether the given user can have a university status.
     *
     * @static
     * @param \CommonBundle\Entity\Users\People\Academic $person the user to check
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The year of the status
     * @return bool
     */
    public static function isValidPerson(Academic $person, AcademicYear $academicYear)
    {
        return ($person != null) && $person->canHaveUniversityStatus($academicYear);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status string the status to set
     * @return \CommonBundle\Entity\Users\UniversityStatus;
     */
    public function setStatus($status)
    {
        if (self::isValidStatus($status))
            $this->status = $status;

        return $this;
    }

    /**
     * Checks whether the given status is valid.
     *
     * @param $status string A status
     * @return bool
     */
    public static function isValidStatus($status)
    {
        return array_key_exists($status, self::$possibleStatuses);
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }
}
