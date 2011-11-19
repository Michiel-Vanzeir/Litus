<?php

namespace Litus\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class LitusEntityCudiSalesServingQueueItemProxy extends \Litus\Entity\Cudi\Sales\ServingQueueItem implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    /** @private */
    public function __load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;

            if (method_exists($this, "__wakeup")) {
                // call this after __isInitialized__to avoid infinite recursion
                // but before loading to emulate what ClassMetadata::newInstance()
                // provides.
                $this->__wakeup();
            }

            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setId($id_)
    {
        $this->__load();
        return parent::setId($id_);
    }

    public function getPerson()
    {
        $this->__load();
        return parent::getPerson();
    }

    public function setPerson($person_)
    {
        $this->__load();
        return parent::setPerson($person_);
    }

    public function getStatus()
    {
        $this->__load();
        return parent::getStatus();
    }

    public function setStatus($status_)
    {
        $this->__load();
        return parent::setStatus($status_);
    }

    public function getPayDesk()
    {
        $this->__load();
        return parent::getPayDesk();
    }

    public function setPayDesk($payDesk_)
    {
        $this->__load();
        return parent::setPayDesk($payDesk_);
    }

    public function getSession()
    {
        $this->__load();
        return parent::getSession();
    }

    public function setSession($session_)
    {
        $this->__load();
        return parent::setSession($session_);
    }

    public function getQueueNumber()
    {
        $this->__load();
        return parent::getQueueNumber();
    }

    public function setQueueNumber($queueNumber_)
    {
        $this->__load();
        return parent::setQueueNumber($queueNumber_);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'person', 'status', 'payDesk', 'session', 'queueNumber');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}