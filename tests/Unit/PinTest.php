<?php


namespace Tests\Unit;

use App\Payments\Billing\Card;
use App\Payments\Billing\Gateways\Pin;

class PinTest extends BaseTest
{

    public function setUp(): void
    {
        $this->customer=[
            "phone"=>'6972621717',
            "email"=>"ibi.elton@gmail.com",
            "address"=>[
                "street"=>"Optasias 6b",
                "city"=>"athens",
                "zip"=>"1922",
                "country"=>"GR"
            ],
        ];

        $this->card=new Card([
            "holder_name"=>"Elton Ibi",
            "number"=>"5520000000000000",
            "month"=>"04",
            "year"=>'2023',
            "cvc"=>"234"
        ]);

        $psp_conf=$this->getPspConfiguration('pin_payments');
        $options=$psp_conf['options'] ?? [];

        $this->gateway=new Pin($options);
    }

    public function testSuccessChargeWithCard()
    {
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='success');

    }

    public function testFailedChargeWithCard()
    {
        $this->card->number='5560000000000001';
        $response=$this->gateway->charge(200, $this->card, $this->customer);
        $this->assertTrue($response->status=='failed');
    }

    public function testSuccessChargeWithToken()
    {
        $this->card->setToken('cus_CpfpZFuEbN9voaTWBQ0WrQ');
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
