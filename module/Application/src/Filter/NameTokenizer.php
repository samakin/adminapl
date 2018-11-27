<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

declare(strict_types=1);

namespace Application\Filter;

#use Zend\Filter\AbstractFilter;
use Phpml\Exception\InvalidArgumentException;
use Phpml\Tokenization\Tokenizer;
use Application\Filter\Lemma;


/**
 * Разбивает наименование товара на токены
 * 
 *
 * @author Daddy
 */
class NameTokenizer implements Tokenizer
{
    
    // Доступные опции фильтра.
    protected $options = [
    ];  
    
    protected $searchReplace = ['(', ')'];
    
    protected $lemmaFilter;

    // Конструктор.
    public function __construct($options = null) 
    {     
        // Задает опции фильтра (если они предоставлены).
        if(is_array($options)) {
            $this->options = $options;
        }    
        
        $this->lemmaFilter = new Lemma();
    }
    
    public function tokenize($value): array
    {
        $text = str_replace($this->searchReplace, ' ', $value);
        
        //$result = preg_split('/[\pZ\pC]+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
//        if ($result === false) {
//            throw new InvalidArgumentException('preg_split failed on: '.$text);
//        }

        $tokens = [];
        preg_match_all('/\w\w+/u', $text, $tokens);
        
        return $this->lemmaFilter->filter($tokens[0]);
    }
    
}
