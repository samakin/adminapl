<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    
    /**
     * TelegrammManager manager.
     * @var Admin\Service\TelegrammManager
     */
    private $telegrammManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($telegrammManager) 
    {
        $this->telegrammManager = $telegrammManager;        
    }   
    
    public function indexAction()
    {
        return [];
    }
    
    /*
     * Telegramm hook
     */
    public function telegrammHookAction()
    {
        $this->telegrammManager->hook();
        exit;        
    }
    
    public function telegrammSetAction()
    {
        $this->telegrammManager->setHook();
        exit;
    }
    
    public function telegrammUnsetAction()
    {
        $this->telegrammManager->unsetHook();
        exit;
    }
    
    public function phpinfoAction()
    {
        return [];
    }
    
    public function memAction()
    {
        
        if (extension_loaded('memcached')){

            $title = 'Memcached';

            $cache  = new \Zend\Cache\Storage\Adapter\Memcached();
            $cache->getOptions()
                    ->setTtl(3600)
                    ->setServers(
                        array(
                            array('localhost', 11211)
                        )
                    );

            $plugin = new \Zend\Cache\Storage\Plugin\ExceptionHandler();
            $plugin->getOptions()->setThrowExceptions(false);
            $cache->addPlugin($plugin);

        } elseif (extension_loaded('memcache')){
                
            $title = 'Memcache';

            $cache  = new \Zend\Cache\Storage\Adapter\Memcache();
            $cache->getOptions()
                    ->setTtl(3600)
                    ->setServers(
                        array(
                            array('localhost', 11211)
                        )
                    );

            $plugin = new \Zend\Cache\Storage\Plugin\ExceptionHandler();
            $plugin->getOptions()->setThrowExceptions(false);
            $cache->addPlugin($plugin);
        }	

        return new ViewModel([
            'title' => $title,
            'mem' => $cache,
        ]);
    }
}
