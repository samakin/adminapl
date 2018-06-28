<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Pricelist
 * @ORM\Entity(repositoryClass="\Application\Repository\SupplierRepository")
 * @ORM\Table(name="price_description")
 * @author Daddy
 */
class PriceDescription {
    
     // Supplier status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="name")   
     */
    protected $name;
    
    /**
     * @ORM\Column(name="article")   
     */
    protected $article;
    
    
    /**
     * @ORM\Column(name="iid")   
     */
    protected $iid;
    
    /**
     * @ORM\Column(name="producer")   
     */
    protected $producer;
    
    /**
     * @ORM\Column(name="title")   
     */
    protected $title;
    
    /**
     * @ORM\Column(name="price")   
     */
    protected $price;
    
    /**
     * @ORM\Column(name="rest")   
     */
    protected $rest;
    
    /**
     * @ORM\Column(name="oem")   
     */
    protected $oem;

    /**
     * @ORM\Column(name="vendor")   
     */
    protected $vendor;

    /**
     * @ORM\Column(name="lot")   
     */
    protected $lot;

    /**
     * @ORM\Column(name="unit")   
     */
    protected $unit;

    /**
     * @ORM\Column(name="car")   
     */
    protected $car;

    /**
     * @ORM\Column(name="bar")   
     */
    protected $bar;

    /**
     * @ORM\Column(name="currency")   
     */
    protected $currency;

    /**
     * @ORM\Column(name="comment")   
     */
    protected $comment;

    /**
     * @ORM\Column(name="weight")   
     */
    protected $weight;

    /**
     * @ORM\Column(name="country")   
     */
    protected $country;

    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;    

    /**
     * @ORM\Column(name="status")  
     */
    protected $status;    
       
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Supplier", inversedBy="priceDescriptions") 
     * @ORM\JoinColumn(name="supplier_id", referencedColumnName="id")
     */
    private $supplier;    
    
    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getName() 
    {
        return $this->name;
    }

    public function setName($name) 
    {
        $this->name = $name;
    }     

    public function getArticle() 
    {
        return $this->article;
    }

    public function setArtice($article) 
    {
        $this->article = (int) $article;
    }     


    public function getIid() 
    {
        return $this->iid;
    }

    public function setIid($iid) 
    {
        $this->iid = (int) $iid;
    }     

    public function getProducer() 
    {
        return $this->producer;
    }

    public function setProducer($producer) 
    {
        $this->producer = (int) $producer;
    }     

    public function getTitle() 
    {
        return $this->title;
    }

    public function setTitle($title) 
    {
        $this->title = (int) $title;
    }     

    public function getPrice() 
    {
        return $this->price;
    }

    public function setPrice($price) 
    {
        $this->price = (int) $price;
    }     

    public function getRest() 
    {
        return $this->rest;
    }

    public function setRest($rest) 
    {
        $this->rest = (int) $rest;
    }     

    public function getOem() 
    {
        return $this->oem;
    }

    public function setOem($oem) 
    {
        $this->oem = (int) $oem;
    }     

    public function getVendor() 
    {
        return $this->vendor;
    }

    public function setVendor($vendor) 
    {
        $this->vendor = (int) $vendor;
    }     

    public function getUnit() 
    {
        return $this->unit;
    }

    public function setUnit($unit) 
    {
        $this->unit = (int) $unit;
    }     

    public function getCar() 
    {
        return $this->car;
    }

    public function setCar($car) 
    {
        $this->car = (int) $car;
    }     

    public function getLot() 
    {
        return $this->lot;
    }

    public function setLot($lot) 
    {
        $this->lot = (int) $lot;
    }     

    public function getBar() 
    {
        return $this->bar;
    }

    public function setBar($bar) 
    {
        $this->bar = (int) $bar;
    }     

    public function getCurrency() 
    {
        return $this->currency;
    }

    public function setCurrency($currency) 
    {
        $this->currency = (int) $currency;
    }     

    public function getComment() 
    {
        return $this->comment;
    }

    public function setComment($comment) 
    {
        $this->comment = (int) $comment;
    }     

    public function getWeight() 
    {
        return $this->weight;
    }

    public function setWeight($weight) 
    {
        $this->weight = (int) $weight;
    }     

    public function getCountry() 
    {
        return $this->country;
    }

    public function setCountry($country) 
    {
        $this->country = (int) $country;
    }     

    public function getDateCreated() 
    {
        return $this->dateCreated;
    }

    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }     
    
    /*
     * Получить поле по значению
     */
    public function getField($value)
    {
        if ($value == $this->getArticle()) return 'article';
        if ($value == $this->getIid()) return 'iid';
        if ($value == $this->getProducer()) return 'producer';
        if ($value == $this->getTitle()) return 'title';
        if ($value == $this->getPrice()) return 'price';
        if ($value == $this->getRest()) return 'rest';
        return;
    }
    
    
    /**
     * Returns status.
     * @return int     
     */
    public function getStatus() 
    {
        return $this->status;
    }

    
    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList() 
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }    
    
    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];
        
        return 'Unknown';
    }    
    
    public function getStatusActive()
    {
        return self::STATUS_ACTIVE;
    }        
    
    public function getStatusRetired()
    {
        return self::STATUS_RETIRED;
    }        
    
    /**
     * Sets status.
     * @param int $status     
     */
    public function setStatus($status) 
    {
        $this->status = $status;
    }   
    
    /*
     * Возвращает связанный supplier.
     * @return \Application\Entity\Supplier
     */    
    public function getSupplier() 
    {
        return $this->supplier;
    }

    /**
     * Задает связанный supplier.
     * @param \Application\Entity\Supplier $supplier
     */    
    public function setSupplier($supplier) 
    {
        $this->supplier = $supplier;
        $supplier->addPriceDescription($this);
    }    
        
}