<?php

namespace System\Web\Mvc\Validator;

class ValidationStack {
    
    protected $value; 
    protected $validators = array();
    protected $error;
    
    public function __construct($value){
        $this->value = $value;
    }

    public function validators(){
        $arrgs = func_get_args();
        if(count($arrgs) > 0){
            $this->validators = $arrgs;
        }
    }
    
    public function isValid(){
        foreach($this->validators as $validator){ 
            $validator->setValue($this->value);
            if(!$validator->isValid()){ 
                $this->error = $validator->getMessage();
                break;
            }
        }
        
        if($this->error){
            return false;
        }
        return true;
    }
    
    public function getError(){
        return $this->error;
    }
}