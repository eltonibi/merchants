<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\VarDumper;


class Cards extends Model
{
    use HasFactory;

    protected $fillable=[
        'holder_name',
        'number',
        'year',
        'month',
        'cvc',
        'type',
        'email',
        'phone',
        'address',
        'city',
        'zip',
        'country',
        'registered_psp',
    ];

    public function token($psp)
    {
        $registered_psp=json_decode($this->attributes['registered_psp'], true);
        if (isset($registered_psp[$psp]['secret_key'])) {
            return $registered_psp[$psp]['secret_key'];
        }
        return null;
    }

    public function customer()
    {
        return [
            'phone'=>$this->attributes['phone'],
            'email'=>$this->attributes['email'],
            'address'=>[
                'address'=>$this->attributes['street'],
                'city'=>$this->attributes['city'],
                'zip'=>$this->attributes['zip'],
                'country'=>$this->attributes['country'],
            ],
        ];
    }
}
