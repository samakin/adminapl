<?php
namespace Company\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * The form for collecting information about Role.
 */
class ContractForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        
        // Define form name
        parent::__construct('contract-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "name" field
        $this->add([           
            'type'  => 'text',
            'name' => 'name',
            'attributes' => [
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Наименование',
            ],
        ]);
        
        $this->add([           
            'type'  => 'text',
            'name' => 'act',
            'attributes' => [
                'id' => 'act',
            ],
            'options' => [
                'label' => 'Номер',               
            ],
        ]);
                        
        $this->add([           
            'type'  => 'date',
            'name' => 'dateStart',
            'attributes' => [
                'id' => 'dateStart'
            ],
            'options' => [
                'label' => 'Дата',
            ],
        ]);
                
        $this->add([            
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Статус',
                'value_options' => [
                    1 => 'Действующий',
                    2 => 'Закрыт',                    
                ]
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Сохранить',
                'id' => 'submit',
            ],
        ]);
        
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        // Add input for "name" field
        $inputFilter->add([
                'name'     => 'name',
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                ],
            ]);                          
        
        $inputFilter->add([
                'name'     => 'act',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                ],
            ]);         
        
        $inputFilter->add([
                'name'     => 'dateStart',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'Date',
                    ],
                ],
            ]);                                  

        $inputFilter->add([
                'name'     => 'status',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'ToInt'],
                ],                
                'validators' => [
                    ['name'=>'InArray', 'options'=>['haystack'=>[1, 2]]]
                ],
            ]); 
        
    }           
}
