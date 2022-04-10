<?php


namespace App\Payments\Billing;


class Error
{

    /**
     * Store errors
     * @var array
     */
    private $errors = array();

    /**
     * Push error to object
     * @param $field
     * @param $message
     */
    public function add($field, $message)
    {
        $this->errors[$field] = $message;
    }

    /**
     * Return all errors
     * @return array
     */
    public function get()
    {
        return $this->errors;
    }
}