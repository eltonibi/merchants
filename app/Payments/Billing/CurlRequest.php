<?php


namespace App\Payments\Billing;

use Symfony\Component\VarDumper\VarDumper;

class CurlRequest
{

    const METHOD_POST='POST';

    /**
     * @var string
     */
    protected $url;

    /**
     * @var curl_init
     */
    protected $ch;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var curl_getinfo
     */
    protected $info;

    /**
     * @var string
     */
    protected $data;

    public $responseBody;

    protected $options=array();

    /**
     * CurlRequest constructor.
     * @param array $options
     */
    public function __construct($options=array())
    {
        $this->options=$options;
    }

    /**
     * Send request to represented PSP
     * @return $this
     * @throws \Exception
     */
    public function send()
    {

        $this->ch=curl_init();

        curl_setopt($this->ch, CURLOPT_URL, $this->url);

        curl_setopt_array($this->ch, $this->options);

        if ($this->method == self::METHOD_POST) {
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->data);
        }

        $this->responseBody=curl_exec($this->ch);

        if ($this->responseBody === false) {
            $ex=new \Exception(curl_error($this->ch), curl_errno($this->ch));
            curl_close($this->ch);
            throw $ex;
        }

        $this->info=curl_getinfo($this->ch);

        curl_close($this->ch);

        return $this;
    }

    /**
     * Return curl info
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set requested url
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url=$url;
        return $this;
    }

    /**
     * Set requested data
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data=$data;
        return $this;
    }

    /**
     * Set http method type
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method=$method;
        return $this;
    }
}