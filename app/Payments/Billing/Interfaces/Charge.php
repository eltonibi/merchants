<?php


namespace App\Payments\Billing\Interfaces;


use App\Payments\Billing\Card;

Interface Charge
{
    public function charge($money, Card $card, $options = array());
}