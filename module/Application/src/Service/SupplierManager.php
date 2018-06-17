<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Zend\ServiceManager\ServiceManager;
use Application\Entity\Supplier;
use Application\Entity\Pricesettings;
use Application\Entity\PriceGetting;
use Application\Entity\BillGetting;
use Application\Entity\RequestSetting;
use Application\Entity\SupplySetting;
use Company\Entity\Office;
use Zend\File\ClassFileLocator;


/**
 * Description of SupplierService
 *
 * @author Daddy
 */
class SupplierManager
{
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Contact manager
     * @var Application\Service\ContactManager
     */
    private $contactManager;
  
    /**
     * Price manager
     * @var Application\Service\PriceManager
     */
    private $priceManager;
  
    /**
     * Raw manager
     * @var Application\Service\RawManager
     */
    private $rawManager;
  
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $contactManager, $priceManager, $rawManager)
    {
        $this->entityManager = $entityManager;
        $this->contactManager = $contactManager;
        $this->priceManager = $priceManager;
        $this->rawManager = $rawManager;
    }
    
    public function addPriceFolder($supplier)
    {
        //Создать папк для прайсов
        $price_data_folder_name = $this->priceManager->getPriceFolder();
        if (!is_dir($price_data_folder_name)){
            mkdir($price_data_folder_name);
        }
        
        $price_supplier_folder_name = $price_data_folder_name.'/'.$supplier->getId();
        if (!is_dir($price_supplier_folder_name)){
            mkdir($price_supplier_folder_name);
        }

        $arx_price_data_folder_name = $this->priceManager->getPriceArxFolder();
        if (!is_dir($arx_price_data_folder_name)){
            mkdir($arx_price_data_folder_name);
        }
        
        $arx_price_supplier_folder_name = $arx_price_data_folder_name.'/'.$supplier->getId();
        if (!is_dir($arx_price_supplier_folder_name)){
            mkdir($arx_price_supplier_folder_name);
        }
    }        
    
    public function addNewSupplier($data) 
    {
        // Создаем новую сущность.
        $supplier = new Supplier();
        $supplier->setName($data['name']);
        $supplier->setAplId($data['aplId']);
        $supplier->setStatus($data['status']);
        
        $currentDate = date('Y-m-d H:i:s');
        $supplier->setDateCreated($currentDate);        
        
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($supplier);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();

        $this->addPriceFolder($supplier);
        
        return $supplier;
    }   
    
    public function getPriceFolder($supplier)
    {
        $this->addPriceFolder($supplier);
        $price_data_folder_name = $this->priceManager->getPriceFolder();
        return $price_data_folder_name.'/'.$supplier->getId();
    }  
    
    public function getPriceArxFolder($supplier)
    {
        $this->addPriceFolder($supplier);
        $arx_price_data_folder_name = $this->priceManager->getPriceArxFolder();
        return $arx_price_data_folder_name.'/'.$supplier->getId();
    }  
    
    public function getLastPriceFile($supplier)
    {
        $path = $this->getPriceFolder($supplier);

        $result = [];
        foreach (new \DirectoryIterator($path) as $fileInfo) {
            if($fileInfo->isDot()) continue;
            $result[$fileInfo->getFilename()] = [
                'path' => $fileInfo->getPathname(), 
                'date' => $fileInfo->getMTime(),
                'size' => $fileInfo->getSize(),
            ];
        }        

        if (!count($result)){
            $arxPath = $this->getPriceArxFolder($supplier);
            foreach (new \DirectoryIterator($arxPath) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                $result[$fileInfo->getFilename()] = [
                    'path' => $fileInfo->getPathname(), 
                    'date' => $fileInfo->getMTime(),
                    'size' => $fileInfo->getSize(),
                ];
            }        
        }
        return $result;
    }
    
    public function updateSupplier($supplier, $data) 
    {
        $supplier->setName($data['name']);
        $supplier->setAplId($data['aplId']);
        $supplier->setStatus($data['status']);

        $this->entityManager->persist($supplier);
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
        
        $this->addPriceFolder($supplier);
    }    
    
    public function removeSupplier($supplier) 
    {   
        
        $contacts = $supplier->getContacts();
        foreach ($contacts as $contact) {
            $this->contactManager->remove($contact);
        }        
        
        $pricesettings = $supplier->getPricesettings();
        foreach ($pricesettings as $pricesetting) {
            $this->removePricesettings($pricesetting);
        }

        $priceGettings = $supplier->getPriceGettings();
        foreach ($priceGettings as $priceGetting) {
            $this->removePriceGetting($priceGetting);
        }

        $requestSettings = $supplier->getRequestSettings();
        foreach ($requestSettings as $requestSetting) {
            $this->removeRequestSetting($requestSetting);
        }

        $supplySettings = $supplier->getSupplySettings();
        foreach ($supplySettings as $supplySetting) {
            $this->removeSupplySetting($supplySetting);
        }

        $raws = $supplier->getRaw();
        foreach ($raws as $raw) {
            $this->rawManager->removeRaw($raw);
        }
        
        $this->entityManager->remove($supplier);
        
        $this->entityManager->flush();
    }    

     // Этот метод добавляет новый контакт.
    public function addContactToSupplier($supplier, $data) 
    {
       $this->contactManager->addNewContact($supplier, $data);
    }   
    
    public function addNewPricesettings($supplier, $data)
    {
        $pricesettings = new Pricesettings();
        $pricesettings->setArtice($data['article']);
        $pricesettings->setIid($data['iid']);
        $pricesettings->setName($data['name']);
        $pricesettings->setPrice($data['price']);
        $pricesettings->setProducer($data['producer']);
        $pricesettings->setRest($data['rest']);
        $pricesettings->setStatus($data['status']);
        $pricesettings->setTitle($data['title']);
        
        $currentDate = date('Y-m-d H:i:s');
        $pricesettings->setDateCreated($currentDate);        
        
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($pricesettings);

        $pricesettings->setSupplier($supplier);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updatePricesettings($pricesettings, $data)
    {
        $pricesettings->setArtice($data['article']);
        $pricesettings->setIid($data['iid']);
        $pricesettings->setName($data['name']);
        $pricesettings->setPrice($data['price']);
        $pricesettings->setProducer($data['producer']);
        $pricesettings->setRest($data['rest']);
        $pricesettings->setStatus($data['status']);
        $pricesettings->setTitle($data['title']);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($pricesettings);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function removePricesettings($pricesettings)
    {
        $this->entityManager->remove($pricesettings);
        $this->entityManager->flush();
    }
    
    public function addNewPriceGetting($supplier, $data)
    {
        $priceGetting = new PriceGetting();
        $priceGetting->setName($data['name']);
        $priceGetting->setFtp($data['ftp']);
        $priceGetting->setFtpLogin($data['ftpLogin']);
        $priceGetting->setFtpPassword($data['ftpPassword']);
        $priceGetting->setEmail($data['email']);
        $priceGetting->setEmailPassword($data['emailPassword']);
        $priceGetting->setLink($data['link']);
        $priceGetting->setStatus($data['status']);
        $priceGetting->setOrderToApl($data['orderToApl']);
        
        $currentDate = date('Y-m-d H:i:s');
        $priceGetting->setDateCreated($currentDate);        
        
        $priceGetting->setSupplier($supplier);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($priceGetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updatePriceGetting($priceGetting, $data)
    {
        $priceGetting->setName($data['name']);
        $priceGetting->setFtp($data['ftp']);
        $priceGetting->setFtpLogin($data['ftpLogin']);
        $priceGetting->setFtpPassword($data['ftpPassword']);
        $priceGetting->setEmail($data['email']);
        $priceGetting->setEmailPassword($data['emailPassword']);
        $priceGetting->setLink($data['link']);
        $priceGetting->setStatus($data['status']);
        $priceGetting->setOrderToApl($data['orderToApl']);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($priceGetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function removePriceGetting($priceGetting)
    {
        $this->entityManager->remove($priceGetting);
        $this->entityManager->flush();
    }
    
    public function addNewBillGetting($supplier, $data)
    {
        $billGetting = new BillGetting();
        $billGetting->setName($data['name']);
        $billGetting->setEmail($data['email']);
        $billGetting->setEmailPassword($data['emailPassword']);
        $billGetting->setStatus($data['status']);
        
        $currentDate = date('Y-m-d H:i:s');
        $billGetting->setDateCreated($currentDate);        
        
        $billGetting->setSupplier($supplier);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($billGetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updateBillGetting($billGetting, $data)
    {
        $billGetting->setName($data['name']);
        $billGetting->setEmail($data['email']);
        $billGetting->setEmailPassword($data['emailPassword']);
        $billGetting->setStatus($data['status']);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($billGetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function removeBillGetting($billGetting)
    {
        $this->entityManager->remove($billGetting);
        $this->entityManager->flush();
    }
    
    public function addNewRequestSetting($supplier, $data)
    {
        $requestSetting = new RequestSetting();
        $requestSetting->setName($data['name']);
        $requestSetting->setDescription($data['description']);
        $requestSetting->setSite($data['site']);
        $requestSetting->setLogin($data['login']);
        $requestSetting->setPassword($data['password']);
        $requestSetting->setMode($data['mode']);
        $requestSetting->setStatus($data['status']);
        
        $currentDate = date('Y-m-d H:i:s');
        $requestSetting->setDateCreated($currentDate);        
        
        $requestSetting->setSupplier($supplier);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($requestSetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updateRequestSetting($requestSetting, $data)
    {
        $requestSetting->setName($data['name']);
        $requestSetting->setDescription($data['description']);
        $requestSetting->setSite($data['site']);
        $requestSetting->setLogin($data['login']);
        $requestSetting->setPassword($data['password']);
        $requestSetting->setMode($data['mode']);
        $requestSetting->setStatus($data['status']);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($requestSetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function removeRequestSetting($requestSetting)
    {
        $this->entityManager->remove($requestSetting);
        $this->entityManager->flush();
    }
    
    public function addNewSupplySetting($supplier, $data)
    {
        $supplySetting = new SupplySetting();
        $supplySetting->setOrderBefore($data['orderBefore']);
        $supplySetting->setSupplyTime($data['supplyTime']);
        $supplySetting->setSupplySat($data['supplySat']);
        $supplySetting->setStatus($data['status']);
        
        $currentDate = date('Y-m-d H:i:s');
        $supplySetting->setDateCreated($currentDate); 
        
        $office = $this->entityManager->getRepository(Office::class)
                ->findOneById($data['office']);
        $supplySetting->setOffice($office);
        
        $supplySetting->setSupplier($supplier);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($supplySetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function updateSupplySetting($supplySetting, $data)
    {
        $supplySetting->setOrderBefore($data['orderBefore']);
        $supplySetting->setSupplyTime($data['supplyTime']);
        $supplySetting->setSupplySat($data['supplySat']);
        $supplySetting->setStatus($data['status']);

        $office = $this->entityManager->getRepository(Office::class)
                ->findOneById($data['office']);
        $supplySetting->setOffice($office);
        
        // Добавляем сущность в менеджер сущностей.
        $this->entityManager->persist($supplySetting);
        
        // Применяем изменения к базе данных.
        $this->entityManager->flush();
    }
    
    public function removeSupplySetting($supplySetting)
    {
        $this->entityManager->remove($supplySetting);
        $this->entityManager->flush();
    }
    
    public function checkPriceFolder($supplier)
    {
        $this->rawManager->checkSupplierPrice($supplier);
    }
}
