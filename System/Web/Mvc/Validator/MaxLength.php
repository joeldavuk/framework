<?php

namespace System\Web\Mvc\Validator;

class MaxLength extends Validator {
    
    protected $length;
    
    public function __construct($length, $errMessage){
        $this->length = $length;
        $this->errMessage = $errMessage;
    }
    
    public function isValid(){
        if(strlen($this->value) > $this->length){
            return false;
        }
        return true;
    }
}