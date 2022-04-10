<?php


namespace App\Payments\Billing\Gateways;

use App\Payments\Billing\CurlRequest;
use App\Payments\Billing\Options;

use Symfony\Component\VarDumper\VarDumper;

abstract class Gateway
{

    /**
     * Money format supported by this gateway.
     * Can be 'dollars' or 'cents'
     *
     * @var string Money format 'dollars' | 'cents'
     */
    public static $money_format='dollars';

    /**
     * The currency supported by the gateway as ISO 4217 currency code.
     *
     * @var string The ISO 4217 currency code
     */
    public static $default_currency;

    protected $options;

    public function __construct($options=array())
    {

        static::$default_currency=$options['currency']
            ?? static::$default_currency;

        $this->options=$options;
    }

    public function money_format()
    {
        $class=get_class($this);
        $ref=new \ReflectionClass($class);
        return $ref->getStaticPropertyValue('money_format');
    }

    public function amount($money)
    {

        if (null === $money) {
            return null;
        }

        $cents=$money * 100;
        if (!is_numeric($money) || $money < 0) {
            throw new \InvalidArgumentException('money amount must be a positive number.');
        }

        if ($this->money_format() == 'cents'){
           return number_format($cents, 0, '', '');
        }else{
           return number_format($money, 2);
        }
    }

    protected function send_request($method='get', $url, $data, array $options=array())
    {
        $request=new CurlRequest($options);
        return $request->setMethod($method)
            ->setUrl($url)
            ->setData($data)
            ->send()
            ->responseBody;
    }
}