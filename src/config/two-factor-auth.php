<?php


return [

    //'model' => \
    'lifetime'   =>  env('TWO_FACTOR_AUTH_TTL',120),
    'verification_route' => 'two-factor-authentication/verify-code',
    'table'     => 'oauth_auth_code',
    'model'     => \Freedom\TwoFactorAuth\Entities\AuthCode::class,
];
