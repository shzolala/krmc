<?php

use App\Models\Accommodation;
use App\Models\Attraction;
use App\Models\Club;
use App\Models\CoachProfile;
use App\Models\Destination;
use App\Models\ProgramReport;
use App\Models\Shelter;
use App\Models\Shop;

return [
    /*
    |--------------------------------------------------------------------------
    | Reviewable morph map aliases
    |--------------------------------------------------------------------------
    |
    | Short type keys stored in reviews.reviewable_type. Add a model class here
    | to allow ratings and comments on that entity.
    |
    */
    'reviewables' => [
        'club' => Club::class,
        'destination' => Destination::class,
        'program' => ProgramReport::class,
        'accommodation' => Accommodation::class,
        'shelter' => Shelter::class,
        'shop' => Shop::class,
        'coach' => CoachProfile::class,
        'attraction' => Attraction::class,
    ],

    'min_rating' => 1,
    'max_rating' => 5,
    'title_max' => 120,
    'body_max' => 2000,
];
