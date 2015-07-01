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

namespace BrBundle\Entity;

use BrBundle\Entity\Product\Order,
    DateInterval,
    DateTime,
    Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\Mapping as ORM,
    InvalidArgumentException;

/**
 * This is the entity for a invoice.
 *
 * @ORM\Entity(repositoryClass="BrBundle\Repository\Invoice")
 * @ORM\Table(name="br.invoices")
 */
class Invoice
{
    /**
     * @var int The invoice's ID
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Order The order for which this invoice is meant
     *
     * @ORM\ManyToOne(targetEntity="BrBundle\Entity\Product\Order")
     * @ORM\JoinColumn(name="product_order", referencedColumnName="id")
     */
    private $order;

    /**
     * @var DateTime The time of creation of this invoice
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var DateTime The time this invoice was paid.
     *
     * @ORM\Column(name="paid_time", type="datetime", nullable=true)
     */
    private $paidTime;

    /**
     * @var ArrayCollection The entries in this invoice
     *
     * @ORM\OneToMany(
     *      targetEntity="BrBundle\Entity\Invoice\InvoiceEntry",
     *      mappedBy="invoice",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $invoiceEntries;

    /**
     * @var int that resembles the version of this invoice.
     *
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string that provides any possible context for the VAT
     *
     * @ORM\Column(type="string")
     */
    private $vatContext;

    /**
     * Creates a new invoice
     *
     * @param Order $order The order to create the invoice for.
     */
    public function __construct(Order $order)
    {
        $this->setOrder($order);
        $this->creationTime = new DateTime();
        $this->setVersion(0);
        $this->setVatContext();

        $this->invoiceEntries = new ArrayCollection();
    }

    public function getInvoiceNumber(EntityManager $entityManager)
    {
        $brNumber = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_number');

        return $this->creationTime->format('Y') . $brNumber . str_pad($this->order->getContract()->getInvoiceNb(), 3, '0', STR_PAD_LEFT);
    }

    public function setVatContext($text = '')
    {
        $this->vatContext = $text;

        return $this;
    }

    public function getVatContext()
    {
        return $this->vatContext;
    }

    /**
     * @param  int  $version
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isPayed()
    {
        return (null !== $this->paidTime);
    }

    /**
     * @return self
     * @throws InvalidArgumentException Exception gets thrown if the invoice is already payed.
     */
    public function setPayed()
    {
        if ($this->isPayed()) {
            throw new InvalidArgumentException('This invoice has already been paid');
        }

        $this->paidTime = new DateTime();

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param  Order $order
     * @return self
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * @return bool
     */
    public function isExpired(EntityManager $entityManager)
    {
        $now = new DateTime();

        return !$this->isPaid() && $now > $this->getExpirationTime($entityManager);
    }

    /**
     * @return DateTime
     */
    public function getExpirationTime(EntityManager $entityManager)
    {
        $expireTime = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('br.invoice_expire_time');

        return $this->getCreationTime()->add(new DateInterval($expireTime));
    }

    /**
     * @return DateTime
     */
    public function getPaidTime()
    {
        return $this->paidTime;
    }

    /**
     * @param  DateTime $paidTime
     * @return self
     */
    public function setPaidTime(DateTime $paidTime)
    {
        if ($this->isPaid()) {
            throw new InvalidArgumentException('This invoice has already been paid');
        }

        $this->paidTime = $paidTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return null !== $this->paidTime;
    }

    /**
     * @return array
     */
    public function getAllEntries()
    {
        return $this->invoiceEntries->toArray();
    }

    /**
     * @return array
     * @note    Only the most recent entries get returned in the array.
     */
    public function getEntries()
    {
        $version = $this->getVersion();

        $array = array();

        foreach ($this->getAllEntries() as $entry) {
            if ($entry->getVersion() == $version) {
                array_push($array, $entry);
            }
        }

        return $array;
    }
}
