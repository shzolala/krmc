<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Responses\LoginResponse;
use App\Http\Responses\RegisterResponse;
use App\Models\User;
use App\Support\MobileNumber;
use App\Support\PanelRegistry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    private function configureActions(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::authenticateUsing(function (Request $request): ?User {
            $mobile = MobileNumber::normalize($request->input(Fortify::username()));

            if ($mobile === null || ! MobileNumber::isValid($mobile)) {
                return null;
            }

            $user = User::query()->where('mobile', $mobile)->first();

            if ($user && Hash::check((string) $request->input('password'), $user->password)) {
                return $user;
            }

            return null;
        });
    }

    private function configureViews(): void
    {
        Fortify::loginView(fn () => view('pages.auth.login', [
            'demoAccounts' => PanelRegistry::switcherEnabled()
                ? PanelRegistry::demoAccounts()
                : collect(),
        ]));
        Fortify::registerView(fn () => view('pages.auth.register'));
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $mobile = MobileNumber::normalize($request->input(Fortify::username())) ?? 'unknown';
            $throttleKey = Str::transliterate(Str::lower($mobile).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
