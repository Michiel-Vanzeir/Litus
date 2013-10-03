<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
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

namespace LogisticsBundle\Component\Validator;

use DateTime,
    Doctrine\ORM\EntityManager;

/**
 * Checks whether no reservation exists yet for the given resource.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 */
class ReservationConflict extends \Zend\Validator\AbstractValidator
{
    /**
     * @const string The error codes
     */
    const CONFLICT_EXISTS = 'conflictExists';
    const INVALID_FORMAT  = 'invalidFormat';

    /**
     * @var array The error messages
     */
    protected $messageTemplates = array(
        self::CONFLICT_EXISTS => 'A conflicting reservation already exists for this resource',
        self::INVALID_FORMAT  => 'One of the dates is not in the correct format',
    );

    /**
     * @var string The start date of the interval
     */
    private $_startDate;

    /**
     * @var string
     */
    private $_format;

    /**
     * @var LogisticsBundle\Entity\Reservation\ReservableResource
     */
    private $_resource;

    /**
     * @var int The id of the reservation to ignore when searching for conflicts; -1 indicates none
     */
    private $_reservationId;

    /**
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Sets validator options
     *
     * @param mixed $token
     * @param string $format
     * @return void
     */
    public function __construct($startDate, $format, $resource, $entityManager, $reservationId = -1)
    {
        parent::__construct(null);

        $this->_startDate = $startDate;
        $this->_format = $format;
        $this->_resource = $resource;
        $this->_entityManager = $entityManager;
        $this->_reservationId = $reservationId;
    }

    /**
     * Returns true if and only if no other reservation exists for the resource that conflicts with the new one.
     *
     * @param mixed $value
     * @param array $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);

        if (($context !== null) && isset($context) && array_key_exists($this->_startDate, $context)) {
            $startDate = $context[$this->_startDate];
        } else {
            $this->error(self::NOT_VALID);
            return false;
        }

        if ($startDate === null) {
            $this->error(self::NOT_VALID);
            return false;
        }

        $repository = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Reservation\ReservableResource');
        $resource = $repository->findOneByName($this->_resource);

        $startDate = DateTime::createFromFormat($this->_format, $startDate);
        $endDate = DateTime::createFromFormat($this->_format, $value);

        if (!$startDate || !$endDate) {
            return false;
        }

        $repository = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Reservation\Reservation');

        $conflicting = $repository->findAllConflictingIgnoringId($startDate, $endDate, $resource, $this->_reservationId);

        if (isset($conflicting[0])) {
            $this->error(self::CONFLICT_EXISTS);
            return false;
        }

        return true;
    }
}