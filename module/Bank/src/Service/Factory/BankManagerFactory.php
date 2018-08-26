<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bank\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Bank\Service\BankManager;
use Bankapi\Service\TochkaApi;


/**
 * Description of BankManagerFactory
 *
 * @author Daddy
 */
class BankManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, 
                    $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $tochkaApi = $container->get(TochkaApi::class);
        
        // Инстанцируем сервис и внедряем зависимости.
        return new BankManager($entityManager, $tochkaApi);
    }
}