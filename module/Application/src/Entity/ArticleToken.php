<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Producer
 * @ORM\Entity(repositoryClass="\Application\Repository\TokenRepository")
 * @ORM\Table(name="article_token")
 * @author Daddy
 */
class ArticleToken {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id")   
     */
    protected $id;
    
    /**
     * @ORM\Column(name="lemma")  
     */
    protected $lemma;        

    /**
     * @ORM\Column(name="status")  
     */
    protected $status;        

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Article", inversedBy="articleTokens") 
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     */
    protected $article;    
    

    public function getId() 
    {
        return $this->id;
    }

    public function setId($id) 
    {
        $this->id = $id;
    }     

    public function getLemma()
    {
        return $this->lemma;
    }
    
    public function setLemma($lemma) 
    {
        $this->lemma = mb_strcut(trim($lemma), 0, 64, 'UTF-8');
    }     

    public function isIntersectLemma()
    {
        if (is_numeric($this->lemma)){
            return false;
        }

        if (mb_strlen($this->lemma, 'utf-8') < 4){
            return false;
        }        
        
        return true;
    }

    public function getStatus() 
    {
        return $this->status;
    }

    public function setStatus($status) 
    {
        $this->status = $status;
    }     

    /**
     * Возвращает связанный article.
     * @return \Application\Entity\Article
     */    
    public function getArticle() 
    {
        return $this->article;
    }

    /**
     * Задает связанный article.
     * @param \Application\Entity\Article $article
     */    
    public function setArticle($article) 
    {
        $this->article = $article;
    }           
    
    /**
     * Возвращает связанный token.
     * @return \Application\Entity\Token
     */    
//    public function getToken() 
//    {
//        return $this->token;
//    }

    /**
     * Задает связанный token.
     * @param \Application\Entity\Token $token
     */    
//    public function setToken($token) 
//    {
//        $this->token = mb_strcut(trim($token), 0, 64, 'UTF-8');
//    }           
    
}
