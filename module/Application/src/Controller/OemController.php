<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Entity\Rawprice;
use Application\Entity\UnknownProducer;
use Application\Entity\Article;
use Application\Entity\OemRaw;
use Zend\View\Model\JsonModel;

use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;

class OemController extends AbstractActionController
{
   
    /**
    * Менеджер сущностей.
    * @var Doctrine\ORM\EntityManager
    */
    private $entityManager;
    
    /**
     * Менеджер производителей.
     * @var Application\Service\ProducerManager 
     */
    private $producerManager;    
    
    /**
     * Менеджер артикулов производителей.
     * @var Application\Service\ArticleManager 
     */
    private $articleManager;    
    
    // Метод конструктора, используемый для внедрения зависимостей в контроллер.
    public function __construct($entityManager, $producerManager, $articleManager, $oemManager) 
    {
        $this->entityManager = $entityManager;
        $this->producerManager = $producerManager;
        $this->articleManager = $articleManager;
        $this->oemManager = $oemManager;
    }    
    
    public function indexAction()
    {
        $bind = $this->entityManager->getRepository(Rawprice::class)
                ->count(['status' => Rawprice::STATUS_PARSED, 'statusOem' => Rawprice::OEM_PARSED]);
        $noBind = $this->entityManager->getRepository(Rawprice::class)
                ->count(['status' => Rawprice::STATUS_PARSED, 'statusOem' => Rawprice::OEM_NEW]);
        $total = $this->entityManager->getRepository(OemRaw::class)
                ->count([]);
                
        return new ViewModel([
            'bind' => $bind,
            'noBind' => $noBind,
            'total' => $total,
        ]);  
    }
    
    public function contentAction()
    {
        ini_set('memory_limit', '512M');
        	        
        $q = $this->params()->fromQuery('search');
        $offset = $this->params()->fromQuery('offset');
        $limit = $this->params()->fromQuery('limit');
        
        $query = $this->entityManager->getRepository(OemRaw::class)
                        ->findAllOem(['q' => $q]);

        $total = count($query->getResult(2));
        
        if ($offset) $query->setFirstResult( $offset );
        if ($limit) $query->setMaxResults( $limit );

        $result = $query->getResult(2);
        
        return new JsonModel([
            'total' => $total,
            'rows' => $result,
        ]);          
    }    
    
    public function viewAction() 
    {       
        $articleId = (int)$this->params()->fromRoute('id', -1);

        if ($articleId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $article = $this->entityManager->getRepository(Article::class)
                ->findOneById($articleId);
        
        if ($article == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $rawpriceCountBySupplier = $this->entityManager->getRepository(Article::class)
                ->rawpriceCountBySupplier($article);
        
        $prevQuery = $this->entityManager->getRepository(Article::class)
                        ->findAllArticle(['prev1' => $article->getCode()]);
        $nextQuery = $this->entityManager->getRepository(Article::class)
                        ->findAllArticle(['next1' => $article->getCode()]);        

        // Render the view template.
        return new ViewModel([
            'article' => $article,
            'rawpriceCountBySupplier' => $rawpriceCountBySupplier,
            'prev' => $prevQuery->getResult(), 
            'next' => $nextQuery->getResult(),
            'articleManager' => $this->articleManager,
        ]);
    }
    
    public function parseAction()
    {
        $rawpriceId = (int)$this->params()->fromRoute('id', -1);
        if ($rawpriceId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $rawprice = $this->entityManager->getRepository(\Application\Entity\Rawprice::class)
                ->findOneById($rawpriceId);
        
        if ($rawprice == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $this->oemManager->addNewOemRawFromRawprice($rawprice);
        
        return new JsonModel([
            'ok',
        ]);          
    }
    
    public function updateOemFromRawAction()
    {
        set_time_limit(0);
        $rawId = $this->params()->fromRoute('id', -1);

        if ($rawId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $raw = $this->entityManager->getRepository(\Application\Entity\Raw::class)
                ->findOneById($rawId);

        if ($raw == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        

        $this->oemManager->grabOemFromRaw($raw);
                
        return new JsonModel([
            'ok',
        ]);          
    }
    
    public function deleteEmptyOemAction()
    {
        $deleted = $this->articleManager->removeEmptyArticles();
                
        return new JsonModel([
            'result' => 'ok-reload',
            'message' => $deleted.' удалено!',
        ]);          
    }
    
    
    public function deleteUnknownAction()
    {
        $page = $this->params()->fromQuery('page', 1);

        $unknownProducerId = $this->params()->fromRoute('id', -1);
        
        $unknownProducer = $this->entityManager->getRepository(UnknownProducer::class)
                ->findOneById($unknownProducerId);        
        if ($unknownProducer == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->producerManager->removeUnknownProducer($unknownProducer);
        
        // Перенаправляем пользователя на страницу "producer".
        return $this->redirect()->toRoute('producer', ['action' => 'unknown'], ['query' => ['page' => $page]]);
    }    

    
}
