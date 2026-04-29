<?php

use App\Livewire\Ad\AdHistoriesLivewire;
use App\Livewire\Alerts\AccessDeniedLive;
use App\Livewire\Alerts\WaitingApprovalLivewire;
use App\Livewire\Dashboard\DashboardLivewire;
use App\Livewire\Days\DayRangesLivewire;
use App\Livewire\Days\DaysLivewire;
use App\Livewire\Message\MessagesHistoryLivewire;
use App\Livewire\Mothers\MotherLivewire;
use App\Livewire\Mothers\MotherSessionHistoryLivewire;
use App\Livewire\Mothers\MothersManagementLivewire;
use App\Livewire\Organizations\OrganizationsLivewire;
use App\Livewire\Organizations\OrganizationUserCheckLivewire;
use App\Livewire\Organizations\OrganizationUsersLivewire;
use App\Livewire\Profile\ProfileLivewire;
use App\Livewire\Settings\SettingsLivewire;
use App\Livewire\TestLivewire;
use App\Livewire\Trimesters\Weeks\WeekLivewire;
use App\Livewire\Trimesters\Weeks\WeeksLivewire;
use App\Livewire\Users\UserCreateLivewire;
use App\Livewire\Users\UserEditLivewire;
use App\Livewire\Users\UsersLivewire;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
    // return redirect(route('login'));
});

Route::get('/test', TestLivewire::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::middleware(['approved'])->group(function () {
        Route::get('/dashboard', DashboardLivewire::class)->name('dashboard');
        Route::get('/user', UsersLivewire::class)->name('users');

        Route::middleware(['notPharmacyAdmin'])->group(function () {
            Route::get('/mothers', MothersManagementLivewire::class)->name('mothers');
            Route::get('/organizations', OrganizationsLivewire::class)->name('organizations');
            Route::get('/organizations/users/{id}', OrganizationUsersLivewire::class)->name('organizations.users');

            Route::get('mothers/{mother_id}', MotherLivewire::class)->name('mothers.show');
            Route::get('mothers/{mother_id}/session/{session_id}/history', MotherSessionHistoryLivewire::class)->name('mothers.history.show');

            Route::get('/trimester/{id}/weeks', WeeksLivewire::class)->name('trimester.weeks');
            Route::get('/trimester/{trimester_id}/weeks/{week_id}', WeekLivewire::class)->name('trimester.week.show');
            Route::get('/days', DaysLivewire::class)->name('days');
            Route::get('days/ranges', DayRangesLivewire::class)->name('days.range');
        });

        // Route::get('/mothers', MotherLivewire::class )->name('mothers');
        Route::get('profile', ProfileLivewire::class)->name('user.profile');
        Route::get('/user/create/{role}', UserCreateLivewire::class)->name('users.create');
        Route::get('/user/create/{role}/{user_id}', UserEditLivewire::class)->name('users.edit');
        Route::get('/test', TestLivewire::class)->name('test');
        Route::get('/messages-histories', MessagesHistoryLivewire::class)->name('messages.histories');
        Route::get('/ad-histories', AdHistoriesLivewire::class)->name('ad.histories');
        Route::get('/settings', SettingsLivewire::class)->name('settings')->middleware('System');
    });

    Route::get('/organizations/user/check', OrganizationUserCheckLivewire::class)->name('organizations.user.check');

    Route::get('/mail', function () {
        $markdown = new Markdown(view(), config('mail.markdown'));

        return $markdown->render('mail.users.reset');
    });
});

Route::get('/error/access-denied', AccessDeniedLive::class)->name('access-denied');
Route::get('/waiting-approval', WaitingApprovalLivewire::class)->name('waiting-approval');
