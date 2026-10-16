<?php

namespace App\Concerns;

use App\Models\User;
use App\Support\MobileNumber;
use App\Support\Username;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'mobile' => $this->mobileRules($userId),
            'username' => $this->usernameRules($userId),
        ];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function mobileRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            MobileNumber::rule(),
            $userId === null
                ? Rule::unique(User::class, 'mobile')
                : Rule::unique(User::class, 'mobile')->ignore($userId),
        ];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function usernameRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'min:3',
            'max:30',
            Username::rule(),
            $userId === null
                ? Rule::unique(User::class, 'username')
                : Rule::unique(User::class, 'username')->ignore($userId),
        ];
    }
}
