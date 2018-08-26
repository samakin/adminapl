<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service;

use Bank\Entity\Statement;
use Company\Entity\BankAccount;
use Zend\Json\Encoder;


/**
 * Description of AplBankService
 *
 * @author Daddy
 */
class AplBankService {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Admin manager
     * @var Admin\Service\AdminManager
     */
    private $adminManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->adminManager = $adminManager;
    }
    
    protected function aplApi()
    {
        return 'https://autopartslist.ru/api/';
        
    }
    
    protected function aplApiKey()
    {
        $settings = $this->adminManager->getSettings();
        return md5(date('Y-m-d').'#'.$settings['apl_secret_key']);
    }
    
    /**
     * Преобразовать в формат АПЛ
     * 
     * @param Bank\Entity\Statement $statement
     * @return array
     */
    public function convertStatementToAplFormat($statement)
    {
        $bankAccount = $this->entityManager->getRepository(BankAccount::class)
                ->findOneByRs($statement->getAccount());
        
        if (!$bankAccount) return;
        
        $result['valueDate']         = $statement->getChargeDate();     //ДатаСписано
        $result['docNum']            = $statement->getPaymentNumber();  //Номер
        $result['docDate']           = $statement->getPaymentDate();    //Дата
        $result['docSum']            = abs($statement->getАmount());         //Сумма
        $result['purpose']           = $statement->getPaymentPurpose(); //НазначениеПлатежа
        
        if ($statement->getАmount() > 0){ //Поступление на счет
            
            $result['payerAcc']          = $statement->getCounterpartyAccountNumber(); //ПлательщикРасчСчет
            $result['payerINN']          = $statement->getСounterpartyInn();           //ПлательщикИНН
            $result['payerKPP']          = $statement->getСounterpartyKpp();           //ПлательщикКПП
            $result['payerName']         = $statement->getCounterpartyName();          //Плательщик1 Плательщик
            $result['payerBankName']     = $statement->getСounterpartyBankName();      //ПлательщикБанк1
            $result['payerBankBic']      = $statement->getCounterpartyBankBik();       //ПлательщикБИК
            //$result['payerBankCorrAcc']  = //
        
            $result['payeeAcc']          = $statement->getAccount();            //ПолучательРасчСчет
            $result['payeeINN']          = $bankAccount->getLegal()->getInn();  //ПолучательИНН
            $result['payeeKPP']          = $bankAccount->getLegal()->getKpp();  //ПолучательКПП
            $result['payeeName']         = $bankAccount->getLegal()->getName(); //Получатель1 Получатель
            $result['payeeBankName']     = $bankAccount->getName();             //ПолучательБанк1
            $result['payeeBankBic']      = $statement->getBik();                //ПолучательБИК
            $result['payeeBankCorrAcc']  = $bankAccount->getKs();               //ПолучательКорсчет
            
        } else { //Списание со счета
            
            $result['payeeAcc']          = $statement->getCounterpartyAccountNumber(); //ПлательщикРасчСчет
            $result['payeeINN']          = $statement->getСounterpartyInn();           //ПлательщикИНН
            $result['payeeKPP']          = $statement->getСounterpartyKpp();           //ПлательщикКПП
            $result['payeeName']         = $statement->getCounterpartyName();          //Плательщик1 Плательщик
            $result['payeeBankName']     = $statement->getСounterpartyBankName();      //ПлательщикБанк1
            $result['payeeBankBic']      = $statement->getCounterpartyBankBik();       //ПлательщикБИК
            //$result['payeeBankCorrAcc']  = //
        
            $result['payerAcc']          = $statement->getAccount();            //ПолучательРасчСчет
            $result['payerINN']          = $bankAccount->getLegal()->getInn();  //ПолучательИНН
            $result['payerKPP']          = $bankAccount->getLegal()->getKpp();  //ПолучательКПП
            $result['payerName']         = $bankAccount->getLegal()->getName(); //Получатель1 Получатель
            $result['payerBankName']     = $bankAccount->getName();             //ПолучательБанк1
            $result['payerBankBic']      = $statement->getBik();                //ПолучательБИК
            $result['payerBankCorrAcc']  = $bankAccount->getKs();               //ПолучательКорсчет
            
        }            
        return $result;
    }
    
    public function dispathBankStatement($statement)
    {
        $transferData = $this->convertStatementToAplFormat($statement);
        
        if (is_array($transferData)){
            $url = $this->aplApi().'bank?api='.$this->aplApiKey();
    
            $client = new Client();
            $client->setUri($url);
            $client->setAdapter($this::HTTPS_ADAPTER);
            $client->setMethod('POST');
            $client->setRawBody(Encoder::encode($transferData));

            $headers = $client->getRequest()->getHeaders();
            $headers->addHeaders([
                 'Content-Type: application/json',
            ]);

            $response = $client->send();

            if ($response->isSuccess()){
                $statement->setSwap1(Statement::SWAP1_TRANSFERED);
                $this->entityManager->persist($statement);
                $this->entityManager->flush($statement);
            }
        }    
        return;
    }


    /**
     * Передача выписки
     * 
     * @return void
     */
    public function sendBankStatement()
    {
        $statements = $this->entityManager->getRepository(Statement::class)
                ->findBy(['swap1' => Statement::SWAP1_TO_TRANSFER])
                ;
        if (count($statements)){
            foreach ($statements as $statement){
                $this->dispathBankStatement($statement);
            }
        }
        
        return;
    }
    
}