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
 * @author Michiel Staessen <michiel.staessen@litus.cc>
 * @author Alan Szepieniec <alan.szepieniec@litus.cc>
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
class ReservationConflictValidator extends \Zend\Validator\AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const CONFLICT_EXISTS      = 'conflictExists';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::CONFLICT_EXISTS      => "A conflicting reservation already exists for this resource.",
    );

    /**
     * The start date of the interval
     * 
     * @var string
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
     * @var \Doctrine\ORM\EntityManager The EntityManager instance
     */
    private $_entityManager = null;

    /**
     * Sets validator options
     *
     * @param  mixed $token
     * @param string $format
     * @return void
     */
    public function __construct($startDate, $format, $resource, $entityManager)
    {
        $this->_startDate = $startDate;
        $this->_format = $format;
        $this->_resource = $resource;
        $this->_entityManager = $entityManager;

        parent::__construct(null);
    }

    /**
     * Returns true if and only if no other reservation exists for the resource that conflicts with the new one.
     *
     * @param  mixed $value
     * @param  array $context
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

        $repository = $this->_entityManager
            ->getRepository('LogisticsBundle\Entity\Reservation\Reservation');
        
        $conflicting = $repository->findAllConflicting($startDate, $endDate, $resource);
        
        if (isset($conflicting[0])) {
            $this->error(self::CONFLICT_EXISTS);
            return false;
        }
        
        return true;
    }
}
