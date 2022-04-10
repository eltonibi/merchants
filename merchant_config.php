<?php

return [
    'skroutz'=>[
        'url'=>'https://www.skroutz.gr',
        'psp'=>[
            'default'=>'pin_payments',
            'support'=>[
                'pin_payments'=>[],
                'stripe'=>[],
            ],
        ]
    ],
    'public'=>[
        'url'=>'https://www.public.gr',
        'psp'=>[
            'default'=>'stripe',
            'support'=>[
                'pin_payments'=>[],
                'stripe'=>[],
            ],
        ]
    ],
];