<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Dev\SwitchDemoAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Panel\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Panel\Admin\UserController as AdminUserController;
use App\Http\Controllers\Panel\RoleDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\ShopController;
use App\Livewire\Panel\EditClubProfile;
use App\Livewire\Panel\EditCoachProfile;
use App\Livewire\Panel\EditShopProfile;
use App\Support\PanelRegistry;
use App\Support\Username;
use Illuminate\Support\Facades\Route;
use Modules\Review\Livewire\ModerateReviews;

Route::get('/', HomeController::class)->name('home');
Route::get('/map', MapController::class)->name('map');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
Route::get('/coaches/{coach}', [CoachController::class, 'show'])->name('coaches.show');

Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');

Route::get('/accommodations', [AccommodationController::class, 'index'])->name('accommodations.index');
Route::get('/accommodations/{accommodation}', [AccommodationController::class, 'show'])->name('accommodations.show');

Route::get('/shelters', [ShelterController::class, 'index'])->name('shelters.index');
Route::get('/shelters/{shelter}', [ShelterController::class, 'show'])->name('shelters.show');

Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::get('/shops/{shop}', [ShopController::class, 'show'])->name('shops.show');

Route::get('/u/{user:username}', [ProfileController::class, 'show'])
    ->where('user', Username::routePattern())
    ->name('profiles.show');

Route::post('/dev/switch-demo-account', SwitchDemoAccountController::class)
    ->name('dev.switch-demo-account');

Route::redirect('/admin/reviews', '/panel/admin/reviews');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', function () {
        return redirect()->route(PanelRegistry::homeRouteFor(auth()->user()));
    })->name('dashboard');
});

Route::middleware('auth')->prefix('panel')->name('panel.')->group(function (): void {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::livewire('/reviews', ModerateReviews::class)->name('reviews');
    });

    Route::middleware('role:climber')->prefix('climber')->name('climber.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
    });

    Route::middleware('role:club')->prefix('club')->name('club.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
        Route::livewire('/profile', EditClubProfile::class)->name('profile');
    });

    Route::middleware('role:coach')->prefix('coach')->name('coach.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
        Route::livewire('/profile', EditCoachProfile::class)->name('profile');
    });

    Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
        Route::livewire('/profile', EditCoachProfile::class)->name('profile');
    });

    Route::middleware('role:shop')->prefix('shop')->name('shop.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
        Route::livewire('/profile', EditShopProfile::class)->name('profile');
    });

    Route::middleware('role:organization')->prefix('organization')->name('organization.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->name('dashboard');
    });
});

require __DIR__.'/settings.php';
