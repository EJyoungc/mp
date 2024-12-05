<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\TestLivewire;
use App\Livewire\Dashboard\DashboardLivewire;
use App\Livewire\Days\DayRangesLivewire;
use App\Livewire\Days\DaysLivewire;
use App\Livewire\Mothers\MotherLivewire;
use App\Livewire\Mothers\MotherSessionHistoryLivewire;
use App\Livewire\Profile\ProfileLivewire;
use App\Livewire\Trimesters\Weeks\WeekLivewire;
use App\Livewire\Trimesters\Weeks\WeeksLivewire;
use App\Livewire\Users\UserCreateLivewire;
use App\Livewire\Users\UserEditLivewire;
use App\Livewire\Users\UsersLivewire;
use Illuminate\Mail\Markdown;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test',TestLivewire::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard1');
});

Route::get('/dashboard', DashboardLivewire::class )->name('dashboard');
Route::get('/user', UsersLivewire::class )->name('users');
Route::get('profile',ProfileLivewire::class )->name('user.profile');
Route::get('mothers/{mother_id}',MotherLivewire::class )->name('mothers.show');
Route::get('mothers/{mother_id}/session/{session_id}/history',MotherSessionHistoryLivewire::class )->name('mothers.history.show');
Route::get('/user/create/{role}', UserCreateLivewire::class )->name('users.create');
Route::get('/user/create/{role}/{user_id}', UserEditLivewire::class )->name('users.edit');
Route::get('/trimester/{id}/weeks',WeeksLivewire::class)->name('trimester.weeks');
Route::get('/trimester/{trimester_id}/weeks/{week_id}',WeekLivewire::class)->name('trimester.week.show');
Route::get('/days',DaysLivewire::class)->name('days');
Route::get('days/ranges',DayRangesLivewire::class)->name('days.range');
Route::get('/mail',function(){
    $markdown = new Markdown(view(),config('mail.markdown'));
    return $markdown->render('mail.users.reset');
});