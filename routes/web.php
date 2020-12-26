<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\InviteMemberController;
use App\Http\Controllers\Auth\InvitedRegisterController;
use App\Http\Controllers\GroupsController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\EventCommentsController;
use App\Http\Controllers\AssignmentsController;

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

    Route::get('/profile/{user:username}', [UsersController::class, 'show'])->name('profile');
    Route::get('/profile/{user:username}/edit', [UsersController::class, 'edit']);
    Route::get('/profile/{user:username}/events/all', [UsersController::class, 'getAllUserEvents']);
    Route::get('/profile/{user:username}/events/joined', [UsersController::class, 'getUserJoinedEvents']);
    Route::patch('/profile/{user:username}', [UsersController::class, 'update']);
    Route::get('/all-members/{group:id}', [UsersController::class, 'index']);
    
    Route::get('/{user:username}/notes', [NotesController::class, 'index']);
    Route::post('/{user:username}/notes', [NotesController::class, 'store']);
    Route::delete('/{user}/notes/{id}', [NotesController::class, 'destroy']);
    
    Route::get('/invite-member', [InviteMemberController::class, 'show'])->name('invite');
    Route::post('/invite-member', [InviteMemberController::class, 'process'])->name('process');

    Route::get('/create-group', [GroupsController::class, 'create']);
    Route::post('/create-group', [GroupsController::class, 'store']);

    Route::get('/change-group', [GroupsController::class, 'index']);
    Route::get('/activate-group/{id}', [UsersController::class, 'activateGroup']);

    Route::get('/{group:id}/events', [EventsController::class, 'index']);
    Route::get('/{group:id}/events/{event:id}', [EventsController::class, 'show']);
    Route::post('/events', [EventsController::class, 'store']);
    Route::delete('/events/{event:id}', [EventsController::class, 'destroy']);
    Route::post('/events/{event:id}/join', [EventsController::class, 'join']);
    Route::post('/events/{event:id}/leave', [EventsController::class, 'leave']);

    Route::get('/{group:id}/assignments', [AssignmentsController::class, 'index']);
    Route::post('/assignments', [AssignmentsController::class, 'store']);

    Route::get('/events/{event:id}/comments', [EventCommentsController::class, 'index']);
    Route::post('/events/{event:id}/comments', [EventCommentsController::class, 'store']);

});

Route::get('/invite-member/{token}', [InviteMemberController::class, 'accept'])->name('accept');
Route::post('/invite-member/{token}', [InvitedRegisterController::class, 'store'])->name('registerInvited');

Route::apiResource('/api/users', UserController::class);