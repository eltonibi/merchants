<?php

namespace Tests\Unit;

use App\Payments\Billing\Card;
use App\Payments\Billing\Gateways\Stripe;

class StripeTest extends BaseTest
{
    public function setUp(): void
    {
        $this->customer=[
            "phone"=>'6971111111',
            "email"=>"ibi.elton@gmail.com",
            "address"=>[
                "street"=>"Zoumpoulion 1",
                "city"=>"athens",
                "zip"=>"1922",
                "country"=>"GR",
                'state' => 'attica'
            ],
        ];

        $this->card=new Card([
            "holder_name"=>"Elton Ibi",
            "number"=>"4242424242424242",
            "month"=>"12",
            "year"=>'2030',
            "cvc"=>"234"
        ]);

        $psp_conf=$this->getPspConfiguration('stripe');
        $options=$psp_conf['options'] ?? [];

        $this->gateway=new Stripe($options);
    }

    public function testSuccessChargeWithCard()
    {
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='success');

    }

    public function testFailedChargeWithCard()
    {
        $this->card->number='4000000000009979';
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='failed');
    }

    public function testSuccessChargeWithToken()
    {
        $this->card->setToken('cus_LTdWf4tiXAfEIe');
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='success');

    }

    public function testFailedChargeWithToken()
    {
        $this->card->setToken('cus_xxxxxxxxxxxxxxxxxx');
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='failed');
    }
}
