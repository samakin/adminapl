<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Bank\Repository;

use Doctrine\ORM\EntityRepository;
use Bank\Entity\Statement;
use Bank\Entity\Balance;

/**
 * Description of BankRepository
 *
 * @author Daddy
 */
class BankRepository extends EntityRepository
{
    /**
     * Получить выборку записей выписки
     * 
     * @param string $q поисковый запрос
     * @param string $rs счет
     * @return object
     */
    public function findStatement($q = null, $rs = null)
    {
        $entityManager = $this->getEntityManager();

        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder->select('s')
            ->from(Statement::class, 's')
            ->orderBy('s.chargeDate', 'DESC')
            ->addOrderBy('s.id', 'DESC')    
                ;
        
        if (is_array($rs)){
            $or = $queryBuilder->expr()->orX();
            foreach ($rs as $account){
                $or->add($queryBuilder->expr()->eq('s.account', trim($account)));
            }
            $queryBuilder->where($or);
        }
        
        if ($q){
            $or = $queryBuilder->expr()->orX();
            $or->add($queryBuilder->expr()->like('s.counterpartyInn', '?1'));
            $or->add($queryBuilder->expr()->like('s.counterpartyName', '?1'));
            $or->add($queryBuilder->expr()->like('s.purpose', '?1'));
            $queryBuilder->setParameter('1', '%' . $q . '%');

            if (is_numeric($q)){
                $or->add($queryBuilder->expr()->eq('FLOOR(s.amount)', floor($q)));
                $or->add($queryBuilder->expr()->eq('FLOOR(s.amount)', -floor($q)));
                
//                $i = -1;
//                while ($i > -10){
//                    if (round($q, $i)){
//                        $or->add($queryBuilder->expr()->eq('ROUND(s.amount, '.$i.')', round($q, $i)));                        
//                        $or->add($queryBuilder->expr()->eq('ROUND(s.amount, '.$i.')', -round($q, $i)));                        
//                    }
//                    $i--;
//                }
            }
            $queryBuilder->andWhere($or);
        }
        
        return $queryBuilder->getQuery();
    }    

    /**
     * Получить текущий остаток по счету 
     * @param string $account
     * @return float
     */
    public function currentBalance($account)
    {
        $entityManger = $this->getEntityManager();
        $queryBuilder = $entityManger->createQueryBuilder();
        
        $queryBuilder->select('b')
                ->from(Balance::class, 'b')
                ->where('b.account = ?1')
                ->setParameter('1', $account)
                ->orderBy('b.dateBalance', 'DESC')
                ->setMaxResults(1)
                ;
        
        $balance = $queryBuilder->getQuery()->getOneOrNullResult();
        
        if ($balance){
            $queryBuilder = $entityManger->createQueryBuilder();
            $queryBuilder->select('SUM(s.amount) as amountSum')
                    ->from(Statement::class, 's')
                    ->where('s.account = ?1')
                    ->andWhere('s.chargeDate >= ?2')
                    ->setParameter('1', $balance->getAccount())
                    ->setParameter('2', $balance->getDateBalance())
                    ->groupBy('s.account')
                    ;
            
            $statement = $queryBuilder->getQuery()->getOneOrNullResult();
            
            if ($statement){
                return $balance->getBalance() + $statement['amountSum'];
            } else {
                return $balance->getBalance();
            }
        }
        
        return 0;                
    }
}