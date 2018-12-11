<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Application\Service;

use Application\Entity\Article;
use Application\Entity\UnknownProducer;
use Application\Entity\Raw;
use Application\Entity\Rawprice;
use Application\Validator\Sigma3;

/**
 * Description of RbService
 *
 * @author Daddy
 */
class ArticleManager
{
    
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     *
     * @var Application\Service\NameManager
     */
    private $nameManager;
  
    /**
     *
     * @var Application\Service\OemManager
     */
    private $oemManager;
  
    // Конструктор, используемый для внедрения зависимостей в сервис.
    public function __construct($entityManager, $nameManager, $oemManager)
    {
        $this->entityManager = $entityManager;
        $this->nameManager = $nameManager;
        $this->oemManager = $oemManager;
    }
    
    /**
     * Добавить новый артикул
     * 
     * @param string $code
     * @param Application\Entity\UnknownProducer $unknownProducer
     * @param bool $flushnow
     */
    public function addArticle($code, $unknownProducer, $flushnow = true)
    {
        $filter = new \Application\Filter\ArticleCode();
        $filteredCode = $filter->filter($code);
        
        $article = $this->entityManager->getRepository(Article::class)
                    ->findOneBy(['code' => $filteredCode, 'unknownProducer' => $unknownProducer->getId()]);

        if ($article == null){

            if (mb_strlen($code, 'utf-8') > 36){
               $result = 'moreThan36';
            }
            // Создаем новую сущность UnknownProducer.
            $article = new Article();
            $article->setCode($filteredCode);            
            $article->setFullCode($code);
            $article->setUnknownProducer($unknownProducer);

            // Добавляем сущность в менеджер сущностей.
            $this->entityManager->persist($article);

            // Применяем изменения к базе данных.
            $this->entityManager->flush($article);
        } else {
            if (mb_strlen($article->getFullCode()) < mb_strlen(trim($code))){
                $article->setFullCode(trim($code));                
                $this->entityManager->persist($article);
                if ($flushnow){
                    $this->entityManager->flush($article);
                }    
            }
        }  
        
        return $article;        
    }        
    
    /**
     * Добавление нового артикула из прайса
     * 
     * @param Application\Entity\Article $rawprice
     * @param bool $flush
     */
    public function addNewArticleFromRawprice($rawprice, $flush = true) 
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
        ini_set('memory_limit', '4096M');
        set_time_limit(1200);
        $startTime = time();
        
        $rawprices = $this->entityManager->getRepository(Rawprice::class)
                ->findBy(['raw' => $raw->getId(), 'code' => null]);
        
        foreach ($rawprices as $rawprice){
            $this->addNewArticleFromRawprice($rawprice, false);
            if (time() > $startTime + 400){
                $this->entityManager->flush();
                return;
            }
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
    public function removeArticle($article, $flush = true) 
    {   
        $oemRaws = $article->getOemRaw();
        foreach ($oemRaws as $oemRaw){
            $this->entityManager->remove($oemRaw);
        }
        
        $this->entityManager->remove($article);
        
        if ($flush){
            $this->entityManager->flush();
        }    
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
            $this->removeArticle($row[0], false);
        }
        
        $this->entityManager->flush();
        
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
    
    /**
     * Средняя цена по строкам прайса
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @return float
     */
    public function rawpricesMeanPrice($rawprices)
    {
        $result = [];
        $rest = 0;
        foreach($rawprices as $rawprice){
            if ($rawprice->getStatus() == Rawprice::STATUS_PARSED && $rawprice->getRealRest()){
                $result[] = $rawprice->getRealPrice() * $rawprice->getRealRest();
                $rest += $rawprice->getRealRest();
            }    
        }

        if ($rest){
            return array_sum($result)/$rest;
        }    
        
        return 0;
    }
    
    /**
     * Вычисление средней цены 
     * 
     * @param Application\Entity\Article
     * @return float 
     */
    public function meanPrice($article)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }
        
        if ($article){
            return $this->rawpricesMeanPrice($article->getRawprice());
        }    
        return 0;
    }
    
    
    /**
     * Разброс цены по строкам по набору строк прайса 
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @return float|null
     */
    public function rawpricesDispersion($rawprices)
    {
        $mean = $this->rawpricesMeanPrice($rawprices);

        $result = [];
        $rest = 0;
        foreach($rawprices as $rawprice){
            if ($rawprice->getStatus() == Rawprice::STATUS_PARSED && $rawprice->getRealRest()){
                $result[] = pow(($rawprice->getRealPrice() - $mean), 2)*$rawprice->getRealRest();
                $rest += $rawprice->getRealRest();
            }    
        }

        if ($rest){
            return sqrt(array_sum($result)/$rest);
        } else {
            return 0;
        } 
        
        return;
    }

    /**
     * Разброс цен строки прайса в артикуле
     * 
     * @param Application\Entity\Article $article
     * @return float
     */
    public function dispersionPrice($article)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }
        
        if ($article){
            return $this->rawpricesDispersion($article->getRawprice());
        }
        
        return;
    }
    
    /**
     * Проверка цены на попадание в диапазон цен
     * 
     * @param Application\Entity\Article $article
     * @param Application\Entity\Rawprice $rawprice
     * 
     * @return bool
     */
    public function inSigma3($price, $meanPrice, $dispersion)
    {
        $validator = new Sigma3();
        
        return $validator->isValid($price, $meanPrice, $dispersion);
    }
    
    /**
     * Сравнение цены строки прайса с артикулом
     * 
     * @param Application\Entity\Article $article
     * @param Application\entity\Rawprice $rawprice
     * 
     * @return bool
     */
    public function priceMatching($article, $rawprice)
    {
        $rawprices = [];
        foreach ($article->getRawprice() as $row){
            $rawprices[$row->getId()] = $row;
        }
        
        if (!array_key_exists($rawprice->getId(), $rawprices)){
            $rawprices[$rawprice->getId()] = $rawprices;
        }
        
        $meanPrice = $this->rawpricesMeanPrice($rawprices);
        $dispersion = $this->rawpricesDispersion($rawprices);
        
        return $this->inSigma3($rawprice->getRealPrice(), $meanPrice, $dispersion);
    }
    
    /**
     * Получить токены списка строк прайса
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @param integer $rawpriceDiff
     * @return array
     */
    public function getRawpricesTokens($rawprices, $rawpriceDiff = 0)
    {
        $result = [];
        foreach ($rawprices as $rawprice){
            if ($rawprice->getStatus() == $rawprice::STATUS_PARSED && $rawprice->getId() != $rawpriceDiff){
                if ($rawprice->getStatusToken() != $rawprice::TOKEN_PARSED){
                    $this->nameManager->addNewTokenFromRawprice($rawprice);
                    return $this->getRawpricesTokens($rawprices, $rawpriceDiff);
                }
                foreach ($rawprice->getTokens() as $token){
                    if ($token->isIntersectLemma()){
                        if (array_key_exists($token->getId(), $result)){
                            $result[$token->getId()] += 1;                        
                        } else {
                            $result[$token->getId()] = 1;                        
                        }
                    }    
                }            
            }
        }

        return $result;        
    }
    
    /**
     * Получить токены артикула
     * 
     * @param Application\Entity\Article|integer $article
     * @param integer $rawpriceDiff Исключение
     * 
     * @return array|null
     */
    public function getTokens($article, $rawpriceDiff = 0)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }
        
        if ($article){
            return $this->getRawpricesTokens($article->getRawprice(), $rawpriceDiff);
        }
        
        return;
    }
    
    /**
     * Сравнить токены списка строк прайсов и строки прайса
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @param Application\Entity\Rawprice $rawprice
     * 
     * @return bool
     */
    public function tokenRawpricesIntersect($rawprices, $rawprice)
    {
       $rawpricesTokens = $this->getRawpricesTokens($rawprices, $rawprice->getId());
       
       if (count($rawpricesTokens)){
            
           if ($rawprice->getStatusToken() != $rawprice::TOKEN_PARSED){
                $this->nameManager->addNewTokenFromRawprice($rawprice);
                return $this->tokenRawpricesIntersect($rawprices, $rawprice);
            }

            $rawpriceTokens = [];
            foreach ($rawprice->getTokens() as $token){
                if ($token->isIntersectLemma()){
                    if (array_key_exists($token->getId(), $rawpriceTokens)){
                        $rawpriceTokens[$token->getId()] += 1;                        
                    } else {
                        $rawpriceTokens[$token->getId()] = 1;                        
                    }
                }    
            }
            
            $inersect = array_intersect_key($rawpricesTokens, $rawpriceTokens);
            //var_dump(count($inersect) > 0);
            return count($inersect) > 0;
       }    
        
       return true;
    }
    
    /**
     * Сравнить токены артикула и строки прайса
     * 
     * @param Application\Entity\Article $article
     * @param Application\Entity\Rawprice $rawprice
     * 
     * @return bool|null
     */
    public function tokenIntersect($article, $rawprice)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }

        if ($article){
            return $this->tokenRawpricesIntersect($article->getRawprice(), $rawprice);
        }
        
        return false;
    }
    
    /**
     * Получить номера из списка строка прайса
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @param integer $rawpriceDiff;
     * 
     * @return array
     */
    public function getOemRawRawprices($rawprices, $rawpriceDiff = 0)
    {
        $result = [];
        foreach ($rawprices as $rawprice){
            if ($rawprice->getStatus() == $rawprice::STATUS_PARSED && $rawprice->getId() != $rawpriceDiff){
                if ($rawprice->getStatusOem() != $rawprice::OEM_PARSED){
                    $this->oemManager->addNewOemRawFromRawprice($rawprice);
                    return $this->getOemRawRawprices($rawprices, $rawpriceDiff);
                }
                foreach ($rawprice->getOemRaw() as $oem){
                    $result[] = $oem->getCode();
                }            
            }
        }

        return $result;        
    }
    
    /**
     * Получить номера артикула
     * 
     * @param Application\Entity\Article|integer $article
     * @param integer $rawpriceDiff Исключение
     * 
     * @return array|null
     */
    public function getOemRaw($article, $rawpriceDiff = 0)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }
        
        if ($article){
            return $this->getOemRawRawprices($article->getRawprice(), $rawpriceDiff);
        }
        
        return;        
    }

    /**
     * 
     * @param Doctrine\Common\Collections\ArrayCollection $rawprices
     * @param Application\Entity\Rawprice $rawprice
     * @return array|null
     */
    public function oemRawpricesIntersect($rawprices, $rawprice)
    {
       $rawpricesOem = $this->getOemRawRawprices($rawprices, $rawprice->getId());
       
       if ($rawpricesOem){
            
           if ($rawprice->getStatusOem() != $rawprice::OEM_PARSED){
                $this->oemManager->addNewOemRawFromRawprice($rawprice);
                return $this->oemRawpricesIntersect($rawprices, $rawprice);
            }

            $rawpriceOem = [];
            foreach ($rawprice->getOemRaw() as $oem){
                $rawpriceOem[] = $oem->getCode();
            }
            
            if (!count($rawpriceOem)){
                return [];
            }
            
            $inersect = array_intersect($rawpricesOem, $rawpriceOem);
            return $inersect;
       }
       
       return [];
        
    }
    
    /**
     * Сравнить номера артикула и строки прайса
     * 
     * @param Application\Entity\Article $article
     * @param Application\Entity\Rawprice $rawprice
     * 
     * @return array|null
     */
    public function oemIntersect($article, $rawprice)
    {
        if (is_numeric($article)){
            $article = $this->entityManager->getRepository(Article::class)
                    ->findOneById($article);
        }

        return $this->oemRawpricesIntersect($article->getRawprice(), $rawprice);
    }
    
}
