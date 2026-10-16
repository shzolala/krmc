<?php

use Laravel\Fortify\Features;

return [

    'guard' => 'web',

    'passwords' => 'users',

    'username' => 'mobile',

    'email' => 'mobile',

    'lowercase_usernames' => false,

    'home' => '/dashboard',

    'prefix' => '',

    'domain' => null,

    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
    ],

    'views' => true,

    'features' => [
        Features::registration(),
        // بازیابی رمز با ایمیل غیرفعال است؛ بعداً با OTP پیامکی جایگزین می‌شود.
    ],

];
