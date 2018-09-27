<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Application\Entity\OemRaw;
use Application\Entity\Article;
use Application\Entity\Raw;
use Application\Entity\Rawprice;

/**
 * Description of RbService
 *
 * @author Daddy
 */
class OemManager
{
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
  
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * Добавить новый код
     * 
     * @param string $code
     * @param Application\Entity\Article $article
     * @param bool $flushnow
     */
    public function addOemRaw($code, $article, $flushnow = true)
    {
        $filter = new \Application\Filter\ArticleCode();
        $filteredCode = $filter->filter($code);
        
        $oem = $this->entityManager->getRepository(OemRaw::class)
                    ->findOneBy(['code' => $filteredCode, 'article' => $article->getId()]);

        if ($oem == null){

            if (mb_strlen($code, 'utf-8') > 36){
               $result = 'moreThan36';
            }
            // Создаем новую сущность UnknownProducer.
            $oem = new OemRaw();
            $oem->setCode($filteredCode);            
            $oem->setFullCode($code);
            $oem->setArticle($article);

            // Добавляем сущность в менеджер сущностей.
            $this->entityManager->persist($oem);

            // Применяем изменения к базе данных.
            $this->entityManager->flush($oem);
        } else {
            if (mb_strlen($oem->getFullCode()) < mb_strlen(trim($code))){
                $oem->setFullCode(trim($code));                
                $this->entityManager->persist($oem);
                if ($flushnow){
                    $this->entityManager->flush($oem);
                }    
            }
        }  
        
        return $oem;        
    }        
    
    /**
     * Добавление нового кода из прайса
     * 
     * @param Application\Entity\Rawprice $rawprice
     * @param bool $flush
     */
    public function addNewOemRawFromRawprice($rawprice, $flush = true) 
    {
        if ($rawprice->getUnknownProducer()){
            $article = $this->addArticle($rawprice->getArticle(), $rawprice->getUnknownProducer(), $flush);

            if ($article){

                $rawprice->setCode($article);
                $this->entityManager->persist($rawprice);

                if ($flush){
                    $this->entityManager->flush();
                }    
            }   
        }    
        
        return;
    }  
    
    /**
     * Выборка артиклей из прайса и добавление их в артиклулы
     */
    public function grabArticleFromRaw($raw)
    {
        ini_set('memory_limit', '2048M');
        
        $rawprices = $this->entityManager->getRepository(Rawprice::class)
                ->findBy(['raw' => $raw->getId(), 'code' => null]);
        
        foreach ($rawprices as $rawprice){
            $this->addNewArticleFromRawprice($rawprice, false);
        }
        $this->entityManager->flush();
        
        $rawprices = $this->entityManager->getRepository(Rawprice::class)
                ->findBy(['raw' => $raw->getId(), 'code' => null]);
        
        if (count($rawprices) === 0){
            $raw->setParseStage(Raw::STAGE_ARTICLE_PARSED);
            $this->entityManager->persist($raw);
            $this->entityManager->flush($raw);
        }        
        
    }
    

    /**
     * Удаление артикула
     * 
     * @param Application\Entity\Article $article
     */
    public function removeArticle($article) 
    {   
        $this->entityManager->remove($article);
        
        $this->entityManager->flush($article);
    }    
    
    /**
     * Поиск и удаление артикулов не привязаных к строкам прайсов
     */
    public function removeEmptyArticles()
    {
        ini_set('memory_limit', '2048M');
        
        $articlesForDelete = $this->entityManager->getRepository(Article::class)
                ->findArticlesForDelete();

        foreach ($articlesForDelete as $row){
            $this->removeArticle($row[0]);
        }
        
        return count($articlesForDelete);
    }    
    
    /**
     * Случайная выборка из прайсов по id артикля и id поставщика 
     * @param array $params
     * @return object      
     */
    public function randRawpriceBy($params)
    {
        return $this->entityManager->getRepository(Article::class)
                ->randRawpriceBy($params);
    }
    
    /**
     * Выборка артикулов из прайсов и добавление их в артикулы
     * привязка к строкам прайса
     * 
     * @param Application\Entity\Raw $raw
     */
    public function grabArticleFromRaw2($raw)
    {
        ini_set('memory_limit', '2048M');

        $articles = $this->entityManager->getRepository(Article::class)
                ->findArticleFromRaw($raw);

        $filter = new \Application\Filter\ArticleCode();

        foreach ($articles as $row){

            $filteredCode = $filter->filter($row['code']);        
            $unknownProducerId = $row['unknownProducer'];
            
            $data = [
                'code' => $filteredCode,
                'fullcode' => trim($row['code']),
                'unknown_producer_id' => $unknownProducerId,
            ];
            try{
                $this->entityManager->getRepository(UnknownProducer::class)
                        ->insertUnknownProducer($data);
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
                //дубликат
            }   
            
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneBy(['code' => $filteredCode, 'unknownProducer' => $unknownProducerId]);
            
            if ($article){
                
                $unknownProducer = $this->entityManager->getRepository(UnknownProducer::class)
                        ->findOneById($unknownProducerId);
                
                $article->setUnknownProducer($unknownProducer);
                $this->entityManager->persist($article);
                
                $rawprices = $this->entityManager->getRepository(Rawprice::class)
                        ->findBy(['raw' => $raw->getId(), 'unknownProducer' => $unknownProducerId, 'code' => $article->getId()]);
                
                foreach ($rawprices as $rawprice){
                    $rawprice->setCode($article);
                    $this->entityManager->persist($rawprice);
                }
            }            
        }
        
        $this->entityManager->flush();
        
        $rawprices = $this->entityManager->getRepository(Raw::class)
                ->findCodeRawprice($raw);
        
        if (count($rawprices) === 0){
            $raw->setParseStage(Raw::STAGE_ARTICLE_PARSED);
            $this->entityManager->persist($raw);
            $this->entityManager->flush($raw);
        }        
    }
    
}
