<?php namespace App\Exceptions;

/**
 * help http://stackoverflow.com/questions/6797142/can-you-throw-an-array-instead-of-a-string-as-an-exception-in-php
 * 
 **/
class ArrException extends \Exception {
    private $_options;

    public function __construct($message, 
                                $code = 0, 
                                Exception $previous = null, 
                                $options = array('params')) 
    {
        parent::__construct($message, $code, $previous);

        $this->_options = $options; 
    }

    public function getOptions() { return $this->_options; }
}