<?php


namespace App\Payments\Billing\Gateways;


use App\Payments\Billing\Card;
use App\Payments\Billing\Interfaces\Charge;
use App\Payments\Billing\Response;
use Symfony\Component\VarDumper\VarDumper;

class Pin extends Gateway implements Charge
{
    const TEST_URL='https://test-api.pinpayments.com/1';

    const CHARGE='/charges';

    /**
     * {@inheritdoc}
     */
    public static $money_format = 'cents';

    /**
     * {@inheritdoc}
     */
    public static $default_currency = 'AUD';

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
        $address=$options['address']??null;

        $this->post['card']['address_line1']=$address['street']??null;
        $this->post['card']['address_city']=$address['city']??null;
        $this->post['card']['address_postcode']=$address['zip']??null;
        $this->post['card']['address_state']=$address['state']??null;
        $this->post['card']['address_country']=$address['country']??null;
    }

    private function addCard(Card $card)
    {

        if (null === $card->token) {
            $this->post['card']['number']=$card->number;
            $this->post['card']['expiry_month']=$card->month;
            $this->post['card']['expiry_year']=$card->year;
            $this->post['card']['cvc']=$card->cvc;
            $this->post['card']['name']=$card->holder_name;
        }

        if (strpos($card->token, 'card_') === 0) {
            return $this->post['card_token'] = $card->token;
        }

        if (strpos($card->token, 'cus_') === 0) {
            return $this->post['customer_token'] = $card->token;
        }
    }

    private function addCustomer($options)
    {
        $this->post['email']=$options['email'] ??null;
        $this->post['ip_address']=$options['ip'] ?? null;
    }

    private function commit($action, $method='POST')
    {
        $url=self::TEST_URL;

        $url.=$action;

        $options=array(
            CURLOPT_USERPWD=>$this->options['secret_key'].":",
            CURLOPT_RETURNTRANSFER=>true,

        );

        $data=$this->send_request($method, $url, $this->postData(), $options);

        $data = json_decode($data, true);

        $response= $data['response']??$data;
        return new Response(
            $this->statusFrom($response),
            $this->messageFrom($response),
            $response
        );

    }

    private function statusFrom($response)
    {
        if ((isset($response['success']) and $response['success'] == 1) || (isset($response['token'])  and $response['token'] != null)) {
            return 'success';
        }
        return 'failed';
    }

    private function messageFrom($response)
    {
        return $response['status_message'] ?? $response['error_description']??null;
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