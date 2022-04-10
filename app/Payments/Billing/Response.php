<?php


namespace App\Payments\Billing;


class Response
{
    /**
     * success,failed
     * @var string
     */
    public $status;

    /**
     * @var
     */
    public $message;

    /**
     * @var array|mixed
     */
    public $body;

    public function __construct($status, $message, $body=array())
    {
        $this->status  =  $status;
        $this->message =  $message;
        $this->body    =  $body;
    }
}