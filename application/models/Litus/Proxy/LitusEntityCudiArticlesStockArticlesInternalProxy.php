<?php

namespace Litus\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class LitusEntityCudiArticlesStockArticlesInternalProxy extends \Litus\Entity\Cudi\Articles\StockArticles\Internal implements \Doctrine\ORM\Proxy\Proxy
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
    
    
    public function canExpire()
    {
        $this->__load();
        return parent::canExpire();
    }

    public function isBookable()
    {
        $this->__load();
        return parent::isBookable();
    }

    public function getId()
    {
        $this->__load();
        return parent::getId();
    }

    public function getTitle()
    {
        $this->__load();
        return parent::getTitle();
    }

    public function getMetaInfo()
    {
        $this->__load();
        return parent::getMetaInfo();
    }

    public function getTimestamp()
    {
        $this->__load();
        return parent::getTimestamp();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'title', 'metaInfo', 'timestamp', 'removed', 'purchasePrice', 'sellPrice', 'sellPriceMembers', 'barcode', 'bookable', 'unbookable', 'supplier', 'canExpire', 'nbBlackAndWhite', 'nbColored', 'binding', 'official', 'rectoVerso', 'frontPageColor');
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