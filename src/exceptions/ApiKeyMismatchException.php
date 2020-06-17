<?php

class ApiKeyMismatchException extends Exception {
    public function __construct($message = 'COM_SMS77API_API_KEY_MISMATCH', $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }
}

