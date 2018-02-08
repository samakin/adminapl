<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use User\Filter\PhoneFilter;

/**
 * Description of Phone
 * @ORM\Entity(repositoryClass="\Application\Repository\ContactRepository")
 * @ORM\Table(name="phone")
 * @author Daddy
 */
class Phone {
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
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
    
    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Contact", inversedBy="phone") 
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     */
    protected $contact;

    
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
        $filter = new PhoneFilter();
        $filter->setFormat(PhoneFilter::PHONE_FORMAT_DB);
        $this->name = $filter->filter($name);
    }     

    /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated     
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    } 

    /*
     * Возвращает связанный contact.
     * @return \Application\Entity\Contact
     */    
    public function getContact() 
    {
        return $this->contact;
    }

    /**
     * Задает связанный contact.
     * @param \Application\Entity\Supplier $contact
     */    
    public function setContact($contact) 
    {
        $this->contact = $contact;
        $contact->addPhone($this);
    }     
        
}
