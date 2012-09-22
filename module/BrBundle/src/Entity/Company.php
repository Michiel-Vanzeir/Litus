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

namespace BrBundle\Entity;

use BrBundle\Entity\Users\People\Corporate,
    CommonBundle\Entity\General\Address,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * This is the entity for a company.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Company")
 * @ORM\Table(name="br.companies")
 */
class Company
{
    /**
     * @var string The company's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string The company's name
     *
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var string The company's VAT number
     *
     * @ORM\Column(type="string", name="vat_number")
     */
    private $vatNumber;

    /**
     * @var \CommonBundle\Entity\General\Address The address of the company
     *
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\General\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="address", referencedColumnName="id")
     */
    private $address;

    /**
     * @var string The history of the company
     *
     * @ORM\Column(type="text")
     */
    private $history;

    /**
     * @var string The description of the company
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string The sector of the company
     *
     * @ORM\Column(type="string")
     */
    private $sector;

    /**
     * @var string The logo of the company
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $logo;

    /**
     * @var bool Whether or not this is an active company
     *
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection The company's contacts
     *
     * @ORM\ManyToMany(targetEntity="BrBundle\Entity\Users\People\Corporate", cascade={"persist"})
     * @ORM\JoinTable(
     *      name="br.companies_contacts_map",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="contact_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $contacts;

    /**
     * @var array The possible sectors of a company
     */
    public static $POSSIBLE_SECTORS = array(
        'research' => 'Research',
        'finance' => 'Finance',
    );

    /**
     * @param string $name The company's name
     * @param string $vatNumber The company's VAT number
     * @param \CommonBundle\Entity\General\Address $address The company's address
     * @param string $history The company's history
     * @param string $description The company's description
     * @param string $sector The company's sector
     */
    public function __construct($name, $vatNumber, Address $address, $history, $description, $sector)
    {
        $this->setName($name);
        $this->setVatNumber($vatNumber);
        $this->setAddress($address);
        $this->setHistory($history);
        $this->setDescription($description);
        $this->setSector($sector);

        $this->active = true;
    }

    /**
     * @return boolean
     */
    public static function isValidSector($sector)
    {
        return array_key_exists($sector, self::$POSSIBLE_SECTORS);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     * @return \BrBundle\Entity\Company
     */
    public function setName($name)
    {
        if ((null === $name) || !is_string($name))
            throw new \InvalidArgumentException('Invalid name');

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $vatNumber
     * @return \BrBundle\Entity\Company
     */
    public function setVatNumber($vatNumber)
    {
        if ((null === $vatNumber) || !is_string($vatNumber))
            throw new \InvalidArgumentException('Invalid VAT number');

        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber()
    {
        return $this->vatNumber;
    }

    /**
     * @param \CommonBundle\Entity\General\Address $address
     * @return \BrBundle\Entity\Company
     */
    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return \CommonBundle\Entity\General\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $history
     * @return \BrBundle\Entity\Company
     */
    public function setHistory($history)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * @return string
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param string $description
     * @return \BrBundle\Entity\Company
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $sector
     * @return \BrBundle\Entity\Company
     */
    public function setSector($sector)
    {
        if (!self::isValidSector($sector))
            throw new \InvalidArgumentException('The sector is not valid.');
        $this->sector = $sector;

        return $this;
    }

    /**
     * @return string
     */
    public function getSector()
    {
        return self::$POSSIBLE_SECTORS[$this->sector];
    }

    /**
     * @return string
     */
    public function getSectorCode()
    {
        return $this->sector;
    }

    /**
     * @param string $logo
     * @return \BrBundle\Entity\Company
     */
    public function setLogo($logo)
    {
        $this->logo = trim($logo, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Deactivates the given company.
     *
     * @return \BrBundle\Entity\Company
     */
    public function deactivate()
    {
        $this->active = false;

        return $this;
    }

    /**
     * @return array
     */
    public function getContacts()
    {
        return $this->contacts->toArray();
    }

    /**
     * @param array $contact The contacts that should be added
     * @return \BrBundle\Entity\Company
     * @throws \InvalidArugmentException
     */
    public function addContact(Corporate $contact)
    {
        if ((null === $contact) || $this->contacts->contains($contact))
            throw new \InvalidArgumentException('Invalid contact');

        $this->contacts->add($contact);

        return $this;
    }
}
