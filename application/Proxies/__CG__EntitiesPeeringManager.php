<?php

namespace Proxies\__CG__\Entities;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class PeeringManager extends \Entities\PeeringManager implements \Doctrine\ORM\Proxy\Proxy
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

    /** @private */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    
    public function setEmailLastSent($emailLastSent)
    {
        $this->__load();
        return parent::setEmailLastSent($emailLastSent);
    }

    public function getEmailLastSent()
    {
        $this->__load();
        return parent::getEmailLastSent();
    }

    public function setEmailsSent($emailsSent)
    {
        $this->__load();
        return parent::setEmailsSent($emailsSent);
    }

    public function getEmailsSent()
    {
        $this->__load();
        return parent::getEmailsSent();
    }

    public function setPeered($peered)
    {
        $this->__load();
        return parent::setPeered($peered);
    }

    public function getPeered()
    {
        $this->__load();
        return parent::getPeered();
    }

    public function setRejected($rejected)
    {
        $this->__load();
        return parent::setRejected($rejected);
    }

    public function getRejected()
    {
        $this->__load();
        return parent::getRejected();
    }

    public function setNotes($notes)
    {
        $this->__load();
        return parent::setNotes($notes);
    }

    public function getNotes()
    {
        $this->__load();
        return parent::getNotes();
    }

    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int) $this->_identifier["id"];
        }
        $this->__load();
        return parent::getId();
    }

    public function setCustomer(\Entities\Customer $customer = NULL)
    {
        $this->__load();
        return parent::setCustomer($customer);
    }

    public function getCustomer()
    {
        $this->__load();
        return parent::getCustomer();
    }

    public function setPeer(\Entities\Customer $peer = NULL)
    {
        $this->__load();
        return parent::setPeer($peer);
    }

    public function getPeer()
    {
        $this->__load();
        return parent::getPeer();
    }

    public function setCreated($created)
    {
        $this->__load();
        return parent::setCreated($created);
    }

    public function getCreated()
    {
        $this->__load();
        return parent::getCreated();
    }

    public function setUpdated($updated)
    {
        $this->__load();
        return parent::setUpdated($updated);
    }

    public function getUpdated()
    {
        $this->__load();
        return parent::getUpdated();
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'email_last_sent', 'emails_sent', 'peered', 'rejected', 'notes', 'created', 'updated', 'id', 'Customer', 'Peer');
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