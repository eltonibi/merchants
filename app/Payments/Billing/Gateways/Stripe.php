<?php


namespace App\Payments\Billing\Gateways;


use App\Payments\Billing\Card;
use App\Payments\Billing\Interfaces\Charge;
use App\Payments\Billing\Response;
use Symfony\Component\VarDumper\VarDumper;

class Stripe extends Gateway implements Charge
{
    const TEST_URL='https://api.stripe.com/v1';

    const CHARGE='/charges';

    /**
     * {@inheritdoc}
     */
    public static $default_currency='USD';

    /**
     * {@inheritdoc}
     */
    public static $money_format='cents';

    /**
     * Contains the main body of the request.
     *
     * @var array
     */
    private $post;

    /**
     * Additional options needed by gateway
     *
     * @var array
     */
    protected $options;

    public function __construct($options=array())
    {

        if (!isset($options['secret_key'])) {
            throw new \InvalidArgumentException('secret_key' . " parameter is required!");
        }

        parent::__construct($options);
    }

    public function charge($amount, Card $card, $options=array())
    {
        //init post variable
        $this->post=array();
        $this->addCard($card);
        $this->addInvoice($amount, $options);
        $this->addCustomer($options);

        if (null === $card->token) {
            $this->addAddress($options);
        }

        return $this->commit(self::CHARGE);
    }

    private function addInvoice($amount, $options)
    {
        $this->post['amount']=$this->amount($amount);
        $this->post['currency']=self::$default_currency;
        $this->post['description']=$options['description'] ?? 'My First Test Charge (created for API docs)';
    }

    private function addAddress($options)
    {
        $address=$options['address'] ?? null;

        $this->post['source']['address_line1']=$address['street'] ?? null;
        $this->post['source']['address_city']=$address['city'] ?? null;
        $this->post['source']['address_zip']=$address['zip'] ?? null;
        $this->post['source']['address_state']=$address['state'] ?? null;
        $this->post['source']['address_country']=$address['country'] ?? null;
    }

    private function addCustomer($options)
    {
        $this->post['receipt_email']=$options['email'] ?? null;
    }

    private function addCard(Card $card)
    {
        if (null === $card->token) {
            $this->post['source']['number']=$card->number;
            $this->post['source']['exp_month']=$card->month;
            $this->post['source']['exp_year']=$card->year;
            $this->post['source']['cvc']=$card->cvc;
            $this->post['source']['name']=$card->holder_name;
        }

        if (strpos($card->token, 'tok_') === 0) {
            return $this->post['source']=$card->token;
        }

        if (strpos($card->token, 'cus_') === 0) {
            return $this->post['customer']=$card->token;
        }
    }

    private function commit($action, $method='POST')
    {

        $url=self::TEST_URL;

        $url.=$action;

        $options=array(
            CURLOPT_USERPWD=>$this->options['secret_key'] . ":",
            CURLOPT_RETURNTRANSFER=>true,

        );

        $data=$this->send_request($method, $url, $this->postData(), $options);

        $response=json_decode($data, true);

        return new Response(
            $this->statusFrom($response),
            $this->messageFrom($response),
            $response
        );

    }

    private function statusFrom($response)
    {
        if (isset($response['status'])) {
            return 'success';
        }
        return 'failed';
    }

    private function messageFrom($response)
    {
        return $response['id'] ?? $response['error']['message'] ?? null;
    }

    /**
     * Adds final parameters to post data and
     * build $this->post to the format that your payment gateway understands
     *
     * @return string
     */
    private function postData()
    {
        $post=array_filter($this->post);

        return http_build_query($post);
    }
}