<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service\ExternalDB;

use Zend\Http\Client;
use Zend\Json\Decoder;
use Zend\Json\Encoder;

/**
 * Description of AutodbManager
 *
 * @author Daddy
 */
class PartsApiManager
{
    
    const URI_PRODUCTION = 'https://partsapi.ru/api.php';
    
    const IMAGE_DIR = './public/img'; //папка для хранения картинок
    const GOOD_IMAGE_DIR = './public/img/goods'; //папка для хранения картинок товаров
    
    const HTTPS_ADAPTER = 'Zend\Http\Client\Adapter\Curl';  
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    private $api_key;
    
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $authParams)
    {
        $this->entityManager = $entityManager;
        $this->api_key = $authParams['key'];
    }
    
    /**
     * Получить uri api
     * 
     * @return string 
     */
    public function getUri()
    {
        return $this::URI_PRODUCTION;
    }    
    
    /**
     * Обработка ошибок
     * @param \Zend\Http\Response $response
     */
    public function exception($response)
    {
        ini_set('memory_limit', '512M');
        
        switch ($response->getStatusCode()) {
            case 400: //Invalid code
                throw new \Exception('Ошибка');
            case 401: //The access token is invalid or has expired
                throw new \Exception('Ошибка');
            case 403: //The access token is missing
                throw new \Exception('Доступ запрещен');
            default:
                $error = Decoder::decode($response->getContent(), \Zend\Json\Json::TYPE_ARRAY);
                $error_msg = $response->getStatusCode();
                if (isset($error['error'])){
                    $error_msg .= ' ('.$error['error'].')';
                }
                if (isset($error['error_description'])){
                    $error_msg .= ' '.$error['error_description'];
                }
                if (isset($error['message'])){
                    $error_msg .= ' '.$error['message'];
                }
                throw new \Exception($error_msg);
        }
        
        throw new \Exception('Неопознаная ошибка');
    }    
    
    
    /**
     * Базовый метод доступа к апи
     * 
     * @param string $action
     * @param array $params
     * @return array|Exception
     */    
    public function getAction($action, $params = null)
    {
        ini_set('memory_limit', '512M');
        
        $uri = $this->getUri().'?act='.$action;
        if (is_array($params)){
            $params['key'] = $this->api_key;
            foreach ($params as $key => $value){
                $uri .= "&$key=$value";
            }    
        }        
//        var_dump($uri); exit;
        $client = new Client();
        $client->setUri($uri);
        $client->setAdapter($this::HTTPS_ADAPTER);
        $client->setMethod('GET');
        
        $headers = $client->getRequest()->getHeaders();
//        $headers->addHeaders([
//            'Content-Type: application/json',
//        ]);

        $client->setHeaders($headers);
        
        $response = $client->send();
        
        if ($response->isOk()){
            return Decoder::decode($response->getBody(), \Zend\Json\Json::TYPE_ARRAY);            
        }

        return $this->exception($response);
        
    }
    
    /**
     * КАТАЛОГ: 
     * Список производителей автомобилей выбранного класса: 
     * passenger - легковые, 
     * commercial - грузовые и коммерческие, 
     * moto - мототехника
     * 
     * @param string $group
     * @return array|Esception
     */
    public function getMakes($group)
    {
        return $this->getAction('getMakes', ['group' => $group]);
    }

    /**
     * КАТАЛОГ: 
     * Список моделей выбранного производителя: 
     * passenger - легковые, 
     * commercial - грузовые и коммерческие, 
     * moto - мототехника
     * 
     * @param integer $makeId
     * @param string $group
     * @return array|Esception
     */
    public function getModels($makeId, $group)
    {
        return $this->getAction('getModels', ['make' => $makeId, 'group' => $group]);
    }

    /**
     * КАТАЛОГ: 
     * Список моделей выбранного производителя: 
     * passenger - легковые, 
     * commercial - грузовые и коммерческие, 
     * moto - мототехника
     * 
     * @param integer $makeId
     * @param integer $modelId
     * @param string $group
     * @return array|Esception
     */
    public function getCars($makeId, $modelId, $group)
    {
        return $this->getAction('getCars', ['make' => $makeId, 'model' => $modelId, 'group' => $group]);
    }

}
