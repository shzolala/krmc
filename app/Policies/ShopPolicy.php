<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function update(User $user, Shop $shop): bool
    {
        return $shop->user_id === $user->id;
    }
}
