<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Support\MobileNumber;
use App\Support\Username;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['mobile'] = MobileNumber::normalize($input['mobile'] ?? null) ?? '';
        $input['username'] = Username::normalize($input['username'] ?? null) ?? '';

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'mobile' => $input['mobile'],
            'username' => $input['username'],
            'password' => $input['password'],
        ]);
    }
}
