<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service;

/**
 * Description of AutoruManager
 *
 * @author Daddy
 */
class AutoruManager {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * Post manager.
     * @var Admin\Service\PostManager
     */
    private $postManager;
    
    /**
     * Telegramm manager.
     * @var Admin\Service\TelegrammManager
     */
    private $telegrammManager;
    
    public function __construct($entityManager, $postManager, $telegrammManager)
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;        
        $this->telegrammManager = $telegrammManager;        
    }
    
    public function postOrder()
    {
        $box = [
            'host' => 'imap.yandex.ru',
            'user' => 'autoru@autopartslist.ru',
            'password' => 'kjdrf4',
        ];
        
        $mail = $this->postManager->read($box);
        if (is_array($mail)){
            foreach($mail as $msg){
                if ($msg['subject'] == 'Заявка на новый товар с портала Авто.ру'&& $msg['content']){
                    $this->telegrammManager->sendMessage(['chat_id' => '189788583', 'text' => $msg['content']]);
                    printf($msg['content']);
                }
            }
        }
    }
}
