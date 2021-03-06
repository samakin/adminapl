<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of Make
 * @ORM\Entity(repositoryClass="\Application\Repository\MakeRepository")
 * @ORM\Table(name="model")
 * @author Daddy
 */

class Model {
    
     // Make status constants.
    const STATUS_ACTIVE       = 1; // Active.
    const STATUS_RETIRED      = 2; // Retired.
    
    const PASSENGER_YES       = 1; //
    const PASSENGER_NO       = 2; //    
    
    const COMMERC_YES       = 1; //
    const COMMERC_NO       = 2; //
    
    const MOTO_YES       = 1; //
    const MOTO_NO       = 2; //
    
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
     * @ORM\Column(name="td_id")   
     */
    protected $tdId;
    
    /**
     * @ORM\Column(name="apl_id")   
     */
    protected $aplId;
    
    /**
     * @ORM\Column(name="fullname")  
     */
    protected $fullName;    

    /**
     * @ORM\Column(name="constructioninterval")  
     */
    protected $interval;    

    /**
     * @ORM\Column(name="status")  
     */
    protected $status;    
       
    /**
     * @ORM\Column(name="passenger")  
     */
    protected $passenger;
    
    /**
     * @ORM\Column(name="commerc")  
     */
    protected $commerc;

    /**
     * @ORM\Column(name="moto")  
     */
    protected $moto;
        
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Make", inversedBy="models") 
     * @ORM\JoinColumn(name="make_id", referencedColumnName="id")
     * 
     */
    protected $make;
    
    /**
     * @ORM\OneToMany(targetEntity="\Application\Entity\Car", mappedBy="model")
     * @ORM\JoinColumn(name="id", referencedColumnName="model_id")
     */
    protected $cars;    
    
    public function __construct() {
        $this->cars = new ArrayCollection();
    }    
    
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

    public function getFullName() 
    {
        return $this->fullName;
    }

    public function setFullName($fullName) 
    {
        $this->fullName = $fullName;
    }     

    public function getInterval() 
    {
        return $this->interval;
    }

    public function setInterval($interval) 
    {
        $this->interval = $interval;
    }     

    public function getTdId() 
    {
        return $this->tdId;
    }

    public function setTdId($tdId) 
    {
        $this->tdId = $tdId;
    }     

    public function getAplId() 
    {
        return $this->aplId;
    }

    public function setAplId($aplId) 
    {
        $this->aplId = $aplId;
    }     
    
    public function getPasenger() 
    {
        return $this->passenger;
    }

    public function setPassenger($passenger) 
    {
        $this->passenger = $passenger;
    }     
    
    public function getCommerc() 
    {
        return $this->commerc;
    }

    public function setCommerc($commerc) 
    {
        $this->commerc = $commerc;
    }     
    
    public function getMoto() 
    {
        return $this->moto;
    }

    public function setMoto($moto) 
    {
        $this->moto = $moto;
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
            self::STATUS_ACTIVE => 'Используется',
            self::STATUS_RETIRED => 'Не используется'
        ];
    }    
    
    /**
     * Returns make status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];
        
        return 'Unknown';
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
     * Возвращает связанный make.
     * @return \Application\Entity\Make
     */    
    public function getMake() 
    {
        return $this->make;
    }
    
    /**
     * Задает связанный make.
     * @param \Application\Entity\Make $make
     */    
    public function setMake($make) 
    {
        $this->make = $make;
        $make->addModel($this);
    }         
    
    /**
     * Возвращает cars для этого model.
     * @return array
     */
    public function getCars() 
    {
        return $this->cars;
    }
    
    /**
     * Добавляет новою car к этому model.
     * @param $car
     */
    public function addCar($car) 
    {
        $this->models[] = $car;
    }
}
