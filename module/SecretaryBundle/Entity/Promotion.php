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

namespace SecretaryBundle\Entity;

use CommonBundle\Entity\General\AcademicYear,
    Doctrine\ORM\Mapping as ORM,
    Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the entity for a promotion year.
 *
 * @ORM\Entity(repositoryClass="SecretaryBundle\Repository\Promotion")
 * @ORM\Table(name="general.promotions")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="inheritance_type", type="string")
 * @ORM\DiscriminatorMap({
 *      "academic"="SecretaryBundle\Entity\Promotion\Academic",
 *      "external"="SecretaryBundle\Entity\Promotion\External"
 * }) */
abstract class Promotion
{
    /**
     * @var The entry's unique identifier
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @var \CommonBundle\Entity\General\AcademicYear The year of the promotion
     *
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\General\AcademicYear")
     * @ORM\JoinColumn(name="academic_year", referencedColumnName="id")
     */
    private $academicYear;

    /**
     * Creates a new promotion for the given year.
     *
     * @param \CommonBundle\Entity\General\AcademicYear $academicYear The academic year of this promotion.
     */
    public function __construct(AcademicYear $academicYear)
    {
        $this->academicYear = $academicYear;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \CommonBundle\Entity\General\AcademicYear
     */
    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    /**
     * @return string
     */
    abstract public function getEmailAddress();

    /**
     * @return string
     */
    abstract public function getFirstName();

    /**
     * @return string
     */
    abstract public function getLastName();

    /**
     * @return string
     */
    abstract public function getFullName();
}
