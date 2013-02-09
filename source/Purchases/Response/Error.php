<?php

namespace Google\Client\Purchases\Response;

use \Google\Client\InitializationInterface;

/**
 * Purchases Api error
 * @author alxmsl
 * @date 2/9/13
 */ 
final class Error implements InitializationInterface {
    /**
     * @var string error code
     */
    private $code = '';

    /**
     * @var string error message
     */
    private $message = '';

    /**
     * @var \stdClass[] errors
     */
    private $errors = array();

    /**
     * Setter for error code
     * @param string $code error code
     * @return Error self
     */
    private function setCode($code) {
        $this->code = $code;
        return $this;
    }

    /**
     * Getter for error code
     * @return string error code
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * Setter for errors
     * @param array $errors errors
     * @return Error self
     */
    private function setErrors(array $errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Getter for errors
     * @return \stdClass[] errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Setter for error message
     * @param string $message error message
     * @return Error error message
     */
    private function setMessage($message) {
        $this->message = $message;
        return $this;
    }

    /**
     * Getter for error message
     * @return string error message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Method for object initialization by the string
     * @param string $string error data string
     * @return Resource error object
     */
    public static function initializeByString($string) {
        $object = json_decode($string);
        $Error = new self();
        $Error->setCode($object->error->code)
            ->setErrors((array) $object->error->errors)
            ->setMessage($object->error->message);
        return $Error;
    }
}
