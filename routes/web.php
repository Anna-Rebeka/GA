<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfilesController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\InviteMemberController;
use App\Http\Controllers\Auth\InvitedRegisterController;
use App\Http\Controllers\GroupsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', function() {
        return redirect('/profile/' . auth()->user()->username);
    })->name('dashboard');

    Route::get('/profile/{user:username}', [ProfilesController::class, 'show'])->name('profile');
    Route::get('/profile/{user:username}/edit', [ProfilesController::class, 'edit']);
    Route::patch('/profile/{user:username}', [ProfilesController::class, 'update']);

    Route::get('/invite-member', [InviteMemberController::class, 'show'])->name('invite');
    Route::post('/invite-member', [InviteMemberController::class, 'process'])->name('process');

    Route::get('/create-group', [GroupsController::class, 'create']);
    Route::post('/create-group', [GroupsController::class, 'store']);
});

Route::get('/invite-member/{token}', [InviteMemberController::class, 'accept'])->name('accept');
Route::post('/invite-member/{token}', [InvitedRegisterController::class, 'store'])->name('registerInvited');

Route::apiResource('/api/users', UserController::class);