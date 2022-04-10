<?php

namespace Tests\Unit;

use App\Payments\Billing\Card;

class CardTest extends BaseTest
{
    public function testValidCard()
    {
        $card=new Card([
            "holder_name"=>"Elton Ibi",
            "number"=>"5520000000000000",
            "month"=>"04",
            "year"=>'2023',
            "cvc"=>"234",
            "description"=>"Order #123"
        ]);
        $this->assertTrue($card->isValid());
    }

    public function testMissingParamsCard()
    {
        $card=new Card([
            "holder_name"=>"Elton Ibi",
            "number"=>"5520000000000000",
            "month"=>"04",
            "year"=>'2023',
        ]);

        $this->assertFalse($card->isValid());
    }
}
