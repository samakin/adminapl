<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\PostController;
use Admin\Service\PostManager;


/**
 * Description of ClientControllerFactory
 *
 * @author Daddy
 */
class PostControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, 
                     $requestedName, array $options = null)
    {
        $postManager = $container->get(PostManager::class);
        
        // Инстанцируем контроллер и внедряем зависимости.
        return new PostController($postManager);
    }
}
