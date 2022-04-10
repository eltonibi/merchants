<?php


namespace App\Payments\Billing;

use Symfony\Component\VarDumper\VarDumper;

class Card
{

    public $holder_name;
    public $month;
    public $year;
    public $cvc;
    public $number;
    public $token;

    /**
     * Store card errors based on validation
     * @var Error
     */
    private $errors;

    /**
     * @var
     */
    private $options;

    public function __construct($options)
    {
        $this->errors=new Error();
        $this->options=$options;

        $this->holder_name=$options['holder_name'] ?? $this->holder_name;
        $this->month=$options['month'] ?? $this->month;
        $this->year=$options['year'] ?? $this->year;
        $this->number=$options['number'] ?? $this->number;
        $this->cvc=$options['cvc'] ?? $this->cvc;
    }

    /**
     * Push errors for missing attributes
     * @param $required
     */
    protected function required($required)
    {
        foreach ($required as $item) {
            if (!isset($this->options[$item]))
                $this->errors->add($item, 'parameter is required!');
        }
    }

    /**
     * Set card token
     * @param $value
     */
    public function setToken($value)
    {
        $this->token=$value;
    }

    /**
     * Check if card has token
     * @return bool
     */
    public function hasToken()
    {

        if (!is_null($this->token)) {
            return true;
        }

        return false;
    }

    /**
     * Return cards errors
     * @return array
     */
    public function errors()
    {
        return $this->errors->get();
    }

    /**
     * Do some basic card validations
     * @return bool
     */
    public function isValid()
    {
        $required=array(
            'holder_name',
            'month',
            'year',
            'number',
            'cvc',
            'description'
        );

        if ($this->token !== null) {
            return true;
        }

        $this->required($required);

        if ($this->holder_name === null || $this->holder_name == "") {
            $this->errors->add('holder_name', 'cannot be empty');
        }

        if (self::isValidMonth($this->month) === false) {
            $this->errors->add('month', 'is not a valid month');
        }

        if (self::isValidExpiryYear($this->year) === false) {
            $this->errors->add('year', 'is not a valid year');
        }

        if (empty($this->errors->get())) {
            return true;
        }

        return false;
    }

    public static function isValidMonth($month)
    {
        $month=(int)$month;
        return ($month >= 1 && $month <= 12);
    }

    public static function isValidExpiryYear($year)
    {
        $year_now=date("Y", time());
        return ($year >= $year_now && $year <= ($year_now + 20));
    }
}