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

namespace BrBundle\Entity\Match;

use BrBundle\Entity\Company;
use CommonBundle\Entity\User\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * This is a wave for a company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Match\CompanyWave")
 * @ORM\Table(name="br_match_companywave")
 */
class CompanyWave
{
    /**
     * @var integer The wave's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Company The company
     *
     * @ORM\ManyToOne(targetEntity="\BrBundle\Entity\Company")
     * @ORM\JoinColumn(name="company", referencedColumnName="id")
     */
    private $company;

    /**
     * @var ArrayCollection The company's contacts
     *
     * @ORM\OneToMany(targetEntity="\BrBundle\Entity\Match", mappedBy="wave")
     * @ORM\JoinColumn(name="matches", referencedColumnName="id")
     */
    private $matches;

    /**
     * @var Wave The wave
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Match\Wave")
     * @ORM\JoinColumn(name="wave", referencedColumnName="id")
     */
    private $wave;

    /**
     * @param Wave $wave
     * @param Company $company
     */
    public function __construct(Wave $wave, Company $company)
    {
        $this->matches = new ArrayCollection();
        $this->wave = $wave;
        $this->company = $company;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getMatches()
    {
        return $this->matches->toArray();
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     * @return self
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return Wave
     */
    public function getWave()
    {
        return $this->wave;
    }
}
