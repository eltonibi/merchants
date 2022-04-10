<?php

namespace App\Http\Controllers;

use App\Interfaces\CardsRepositoryInterface;
use App\Payments\Billing\Gateways\Base;
use Illuminate\Http\Request;
use App\Payments\Billing\Card;
use Illuminate\Http\JsonResponse;
use Symfony\Component\VarDumper\VarDumper;
use function Symfony\Component\String\s;

class PaymentsController extends Controller
{
    private CardsRepositoryInterface $cardRepository;

    /**
     * Init card repository
     * PaymentsController constructor.
     * @param CardsRepositoryInterface $cardRepository
     */
    public function __construct(CardsRepositoryInterface $cardRepository)
    {
        $this->cardRepository=$cardRepository;
    }

    /**
     * Get default Payment Service Provider for merchant
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    protected function getDefaultMerchantPSP($name)
    {
        $config=include("../merchant_config.php");
        if ($config[$name]['psp']['default']) {
            return $config[$name]['psp']['default'];
        }
        throw new \Exception("Unregistered default PSP for MERCHANT {$name} ");
    }

    /**
     * Get Payment Service Provider configurations info
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    protected function getPspConfiguration($name)
    {
        $config=include("../psp_config.php");
        if ($config[$name]) {
            return $config[$name];
        }
        throw new \Exception("Unregistered PSP {$name} ");
    }

    /**
     * Create charge base on requested params
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function charge(Request $request): JsonResponse
    {
//        $merchant='public';
        $merchant='skroutz';
        $customer=[
            "phone"=>'6972621717',
            "email"=>"ibi.elton@gmail.com",
            "address"=>[
                "street"=>"Optasias 6b",
                "city"=>"athens",
                "zip"=>"1922",
                "country"=>"GR"
            ],
        ];

        $psp_name=$this->getDefaultMerchantPSP($merchant);//PSP name Pin,Stripe,..

        $amount=$request->get('amount');//charged amount
        $number=$request->get('number');//card number

        $token=null;

        $saved_card=$this->cardRepository->get($number);//Get from repository card info
        if ($saved_card) {
            $token=$saved_card->token($psp_name);//set token variable with founded psp token for requested card
        }

        $info=$request->toArray(); //Request info example: {"number":"5560000000000001","year":"2023","month":"4","holder_name":"Elton Ibi","cvc":"234","amount":"100","description":"Order #123"}

        $card=new Card($info);
        $card->setToken($token);

        if ($card->isValid() || $card->hasToken()) {//If the card has no error or has token then proceed to gateway
            $psp_conf=$this->getPspConfiguration($psp_name);//Get PSP configuration info like secret_key,className
            $options=$psp_conf['options'] ?? [];
            $className=$psp_conf['className'] ?? [];
            $gateway=Base::gateway($className, $options);//Load represented PSP based on className
            $data=$gateway->charge($amount, $card, $customer);//Charge customer
            return response()->json($data);
        } else {
            return response()->json(['message'=>'invalid_card', 'errors'=>$card->errors()],400);
        }

    }
}
