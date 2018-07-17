<?php

namespace DoctrineORMModule\Proxy\__CG__\Application\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Supplier extends \Application\Entity\Supplier implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', 'id', 'aplId', 'name', 'info', 'status', 'address', 'dateCreated', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'contacts', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'raw', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'priceDescriptions', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'priceGettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'billGettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'requestSettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'supplySettings'];
        }

        return ['__isInitialized__', 'id', 'aplId', 'name', 'info', 'status', 'address', 'dateCreated', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'contacts', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'raw', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'priceDescriptions', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'priceGettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'billGettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'requestSettings', '' . "\0" . 'Application\\Entity\\Supplier' . "\0" . 'supplySettings'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Supplier $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', [$id]);

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriceFolder()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPriceFolder', []);

        return parent::getPriceFolder();
    }

    /**
     * {@inheritDoc}
     */
    public function getArxPriceFolder()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getArxPriceFolder', []);

        return parent::getArxPriceFolder();
    }

    /**
     * {@inheritDoc}
     */
    public function getAplId()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAplId', []);

        return parent::getAplId();
    }

    /**
     * {@inheritDoc}
     */
    public function setAplId($aplId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAplId', [$aplId]);

        return parent::setAplId($aplId);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', []);

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', [$name]);

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getInfo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getInfo', []);

        return parent::getInfo();
    }

    /**
     * {@inheritDoc}
     */
    public function setInfo($info)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setInfo', [$info]);

        return parent::setInfo($info);
    }

    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddress', []);

        return parent::getAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddress', [$address]);

        return parent::setAddress($address);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusAsString()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusAsString', []);

        return parent::getStatusAsString();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusName($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusName', [$status]);

        return parent::getStatusName($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusActive()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusActive', []);

        return parent::getStatusActive();
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusRetired()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatusRetired', []);

        return parent::getStatusRetired();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$status]);

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getDateCreated()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getDateCreated', []);

        return parent::getDateCreated();
    }

    /**
     * {@inheritDoc}
     */
    public function setDateCreated($dateCreated)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setDateCreated', [$dateCreated]);

        return parent::setDateCreated($dateCreated);
    }

    /**
     * {@inheritDoc}
     */
    public function getContacts()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContacts', []);

        return parent::getContacts();
    }

    /**
     * {@inheritDoc}
     */
    public function addContact($contact)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addContact', [$contact]);

        return parent::addContact($contact);
    }

    /**
     * {@inheritDoc}
     */
    public function getLegalContacts()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLegalContacts', []);

        return parent::getLegalContacts();
    }

    /**
     * {@inheritDoc}
     */
    public function getLegalContact()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLegalContact', []);

        return parent::getLegalContact();
    }

    /**
     * {@inheritDoc}
     */
    public function getOtherContacts()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOtherContacts', []);

        return parent::getOtherContacts();
    }

    /**
     * {@inheritDoc}
     */
    public function getRaw()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRaw', []);

        return parent::getRaw();
    }

    /**
     * {@inheritDoc}
     */
    public function addRaw($raw)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRaw', [$raw]);

        return parent::addRaw($raw);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriceDescriptions()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPriceDescriptions', []);

        return parent::getPriceDescriptions();
    }

    /**
     * {@inheritDoc}
     */
    public function addPriceDescription($priceDescription)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addPriceDescription', [$priceDescription]);

        return parent::addPriceDescription($priceDescription);
    }

    /**
     * {@inheritDoc}
     */
    public function getPriceGettings()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPriceGettings', []);

        return parent::getPriceGettings();
    }

    /**
     * {@inheritDoc}
     */
    public function addPriceGettings($priceGetting)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addPriceGettings', [$priceGetting]);

        return parent::addPriceGettings($priceGetting);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillGettings()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBillGettings', []);

        return parent::getBillGettings();
    }

    /**
     * {@inheritDoc}
     */
    public function addBillGettings($billGetting)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addBillGettings', [$billGetting]);

        return parent::addBillGettings($billGetting);
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestSettings()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRequestSettings', []);

        return parent::getRequestSettings();
    }

    /**
     * {@inheritDoc}
     */
    public function addRequestSetting($requestSetting)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addRequestSetting', [$requestSetting]);

        return parent::addRequestSetting($requestSetting);
    }

    /**
     * {@inheritDoc}
     */
    public function getSupplySettings()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSupplySettings', []);

        return parent::getSupplySettings();
    }

    /**
     * {@inheritDoc}
     */
    public function addSupplySetting($supplySetting)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addSupplySetting', [$supplySetting]);

        return parent::addSupplySetting($supplySetting);
    }

}
