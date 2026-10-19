<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\AttractionController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\Dev\SwitchDemoAccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Panel\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Panel\Admin\FinanceController as AdminFinanceController;
use App\Http\Controllers\Panel\Admin\ImpersonationController;
use App\Http\Controllers\Panel\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Panel\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Panel\Admin\UserController as AdminUserController;
use App\Http\Controllers\Panel\FinanceController;
use App\Http\Controllers\Panel\HomeController as PanelHomeController;
use App\Http\Controllers\Panel\MembershipController;
use App\Http\Controllers\Panel\RoleDashboardController;
use App\Http\Controllers\PeakController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SponsorController;
use App\Livewire\Panel\EditClubProfile;
use App\Livewire\Panel\EditCoachProfile;
use App\Livewire\Panel\EditShopProfile;
use App\Livewire\Panel\EditSponsorProfile;
use App\Support\Admin\AdminResources;
use App\Support\PanelRegistry;
use App\Support\Username;
use Illuminate\Support\Facades\Route;
use Modules\Review\Livewire\ModerateReviews;

Route::get('/', HomeController::class)->name('home');
Route::get('/map', MapController::class)->name('map');
Route::get('/peaks', [PeakController::class, 'index'])->name('peaks.index');
Route::get('/membership-plans', PricingController::class)->name('pricing');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/sponsors', [SponsorController::class, 'index'])->name('sponsors.index');
Route::get('/sponsors/{sponsor}', [SponsorController::class, 'show'])->name('sponsors.show');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
Route::get('/coaches/{coach}', [CoachController::class, 'show'])->name('coaches.show');

Route::get('/programs', [ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{program}', [ProgramController::class, 'show'])->name('programs.show');

Route::get('/regions', [RegionController::class, 'index'])->name('regions.index');
Route::get('/regions/{region}', [RegionController::class, 'show'])->name('regions.show');

Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');

Route::get('/attractions', [AttractionController::class, 'index'])->name('attractions.index');
Route::get('/attractions/{attraction}', [AttractionController::class, 'show'])->name('attractions.show');

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
    Route::get('/', PanelHomeController::class)->name('home');
    Route::get('/finance', FinanceController::class)->name('finance');
    Route::livewire('/sponsorship', EditSponsorProfile::class)->name('sponsorship');
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership');
    Route::post('/membership/{plan}', [MembershipController::class, 'choose'])->name('membership.choose');
    Route::post('/impersonation/switch', [ImpersonationController::class, 'switchDemo'])->name('impersonation.switch');
    Route::post('/impersonation/stop', [ImpersonationController::class, 'stop'])->name('impersonation.stop');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/roles', AdminRoleController::class)->name('roles');
        Route::get('/finance', AdminFinanceController::class)->name('finance');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/impersonate', [ImpersonationController::class, 'start'])->name('users.impersonate');

        Route::prefix('manage/{resource}')
            ->whereIn('resource', AdminResources::keys())
            ->whereNumber('id')
            ->name('resources.')
            ->group(function (): void {
                Route::get('/', [AdminResourceController::class, 'index'])->name('index');
                Route::get('/create', [AdminResourceController::class, 'create'])->name('create');
                Route::post('/', [AdminResourceController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [AdminResourceController::class, 'edit'])->name('edit');
                Route::put('/{id}', [AdminResourceController::class, 'update'])->name('update');
                Route::delete('/{id}', [AdminResourceController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/toggle/{field}', [AdminResourceController::class, 'toggle'])->name('toggle');
            });

        Route::livewire('/reviews', ModerateReviews::class)->name('reviews');
    });

    Route::middleware('role:climber')->prefix('climber')->name('climber.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->defaults('section', 'climber')->name('dashboard');
    });

    Route::middleware('role:club')->prefix('club')->name('club.')->group(function (): void {
        Route::get('/{club?}', RoleDashboardController::class)->defaults('section', 'club')->name('dashboard');
        Route::livewire('/{club}/profile', EditClubProfile::class)->name('profile');
    });

    Route::middleware('role:coach')->prefix('coach')->name('coach.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->defaults('section', 'coach')->name('dashboard');
        Route::livewire('/profile', EditCoachProfile::class)->name('profile');
    });

    Route::middleware('role:leader')->prefix('leader')->name('leader.')->group(function (): void {
        Route::get('/', RoleDashboardController::class)->defaults('section', 'leader')->name('dashboard');
        Route::livewire('/profile', EditCoachProfile::class)->name('profile');
    });

    Route::middleware('role:shop')->prefix('shop')->name('shop.')->group(function (): void {
        Route::get('/{shop?}', RoleDashboardController::class)->defaults('section', 'shop')->name('dashboard');
        Route::livewire('/{shop}/profile', EditShopProfile::class)->name('profile');
    });

});

require __DIR__.'/settings.php';
