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
use App\Http\Controllers\AssignmentsCommentsController;
use App\Http\Controllers\ChatroomsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\AssignmentsFilesController;
use App\Http\Controllers\GroupWhiteboardPostsController;
use App\Http\Controllers\GroupStatisticsController;

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

Route::middleware(['auth', 'can.see'])->group(function() {
    Route::get('/dashboard', function() {
        return redirect('/profile/' . auth()->user()->username);
    })->name('dashboard');

    Route::get('/profile/{user:username}', [UsersController::class, 'show'])->name('profile');
    Route::get('/profile/{user:username}/edit', [UsersController::class, 'edit']);
    Route::post('/profile/{user:username}/edit', [UsersController::class, 'update']);
    Route::get('/profile/{user:username}/events/all', [UsersController::class, 'getAllUserEvents']);
    Route::get('/profile/{user:username}/events/joined', [UsersController::class, 'getUserJoinedEvents']);
    Route::get('/profile/{user:username}/assignments/all', [UsersController::class, 'getAllUsersAssignments']);
    Route::get('/profile/{user:username}/assignments/mine', [UsersController::class, 'getUsersAssignments']);
    Route::get('/{user:username}/groups', [UsersController::class, 'getAllUsersGroups']);
    Route::get('/groups/{group:id}/all-members', [UsersController::class, 'index']);
    Route::get('/{user:username}/active-group', [UsersController::class, 'getActiveGroup']);
    Route::get('/profile/{user:id}/settings', [UsersController::class, 'showSettings']);
    Route::post('/profile/{user:id}/settings', [UsersController::class, 'updateSettings']);
    Route::delete('/profile/{user:id}/delete-profile', [UsersController::class, 'destroy']);

    Route::patch('/activate-group/{id}', [UsersController::class, 'activateGroup']);
    
    Route::get('/{user:username}/notes', [NotesController::class, 'index']);
    Route::post('/{user:username}/notes', [NotesController::class, 'store']);
    Route::delete('/{user}/notes/{id}', [NotesController::class, 'destroy']);
    
    Route::get('/groups/{group:id}/invite-member', [InviteMemberController::class, 'show'])->name('invite');
    Route::post('/groups/{group:id}/invite-member', [InviteMemberController::class, 'process'])->name('process');
    Route::delete('/groups/{group:id}/exclude-member/{user:id}', [GroupsController::class, 'excludeMember']);

    Route::get('/create-group', [GroupsController::class, 'create']);
    Route::post('/create-group', [GroupsController::class, 'store']);
    Route::delete('/groups/{group:id}/destroy', [GroupsController::class, 'destroy']);

    Route::get('/groups/{group:id}/edit-group', [GroupsController::class, 'edit']);
    Route::post('/groups/{group:id}/edit-group', [GroupsController::class, 'update']);
    Route::get('/groups/{group:id}/members/get', [GroupsController::class, 'getMembers']);
    Route::get('/groups/{group:id}/whiteboard', [GroupsController::class, 'showWhiteboard']);
    
    Route::get('/groups/{group:id}/get-whiteboard-posts', [GroupWhiteboardPostsController::class, 'getPosts']);
    Route::post('/groups/{group:id}/whiteboard-post', [GroupWhiteboardPostsController::class, 'store']);
    Route::patch('/groups/{group:id}/update-whiteboard', [GroupsController::class, 'updateBoard']);
    Route::get('/groups/{group:id}/loadOlderPosts/{howManyDisplayed}', [GroupWhiteboardPostsController::class, 'loadOlderPosts']);
    Route::delete('/groups/{group:id}/whiteboard-post-delete/{id}', [GroupWhiteboardPostsController::class, 'destroy']);
    
    Route::get('/events', [EventsController::class, 'index']);
    Route::get('/events/{event:id}', [EventsController::class, 'show']);
    Route::post('/events', [EventsController::class, 'store']);
    Route::get('/events/{group:id}/loadOlderEvents/{howManyDisplayed}', [EventsController::class, 'loadOlderEvents']);

    Route::get('/events/{event:id}/edit', [EventsController::class, 'edit']);
    Route::patch('/events/{event:id}/edit', [EventsController::class, 'update']);
    Route::delete('/events/{event:id}', [EventsController::class, 'destroy']);
    Route::post('/events/{event:id}/join', [EventsController::class, 'join']);
    Route::post('/events/{event:id}/leave', [EventsController::class, 'leave']);

    Route::get('/events/{event:id}/comments', [EventCommentsController::class, 'index']);
    Route::post('/events/{event:id}/comments', [EventCommentsController::class, 'store']);
    Route::delete('/events/{event:id}/comments/{id}/destroy', [EventCommentsController::class, 'destroy']);


    Route::get('/assignments', [AssignmentsController::class, 'index']);
    Route::get('/assignments/{assignment:id}', [AssignmentsController::class, 'show']);
    Route::get('/assignments/{assignment:id}/is-taken-by-auth', [AssignmentsController::class, 'checkAssignmentUser']);
    Route::post('/assignments', [AssignmentsController::class, 'store']);
    Route::get('/assignments/{group:id}/loadOlderAssignments/{howManyDisplayed}', [AssignmentsController::class, 'loadOlderAssignments']);

    Route::delete('/assignments/{assignment:id}', [AssignmentsController::class, 'destroy']);
    Route::get('/assignments/{assignment:id}/edit', [AssignmentsController::class, 'edit']);
    Route::patch('/assignments/{assignment:id}/edit', [AssignmentsController::class, 'update']);
    Route::patch('/assignments/{assignment:id}/take', [AssignmentsController::class, 'take']);
    Route::patch('/assignments/{assignment:id}/done', [AssignmentsController::class, 'done']);
    Route::patch('/assignments/{assignment:id}/edit-comment', [AssignmentsController::class, 'updateComment']);


    Route::get('/assignments/{assignment:id}/comments', [AssignmentsCommentsController::class, 'index']);
    Route::post('/assignments/{assignment:id}/comments', [AssignmentsCommentsController::class, 'store']);
    Route::delete('/assignments/{assignment:id}/comments/{id}/destroy', [AssignmentsCommentsController::class, 'destroy']);

    Route::get('/assignments/{assignment:id}/get-files', [AssignmentsFilesController::class, 'index']);
    Route::post('/assignments/{assignment:id}/file-upload', [AssignmentsFilesController::class, 'store']);
    Route::delete('/assignments/file-delete/{id}', [AssignmentsFilesController::class, 'destroy']);

    Route::get('/group-statistics', [GroupStatisticsController::class, 'showStatistics']);
    Route::get('/groups/{group:id}/get-assignments-statistic', [GroupStatisticsController::class, 'getAssignmentsStatistic']);

    Route::get('/chats', [ChatroomsController::class, 'index']);
    Route::get('/chats/{chatroom:id}/latestMessage', [ChatroomsController::class, 'getLatestMessage']);
    Route::get('/chats/{chatroom:id}', [ChatroomsController::class, 'show'])->name('showchat');
    Route::get('/chats/{chatroom:id}/messages', [ChatroomsController::class, 'getMessages']);
    Route::get('/chats/{chatroom:id}/loadOlderMessages/{howManyDisplayed}', [ChatroomsController::class, 'loadOlderMessages']);

    Route::get('/chats/{chatroom:id}/users/{user:id}', [ChatroomsController::class, 'getUsers']);
    Route::patch('/chats/{chatroom:id}/readAll', [ChatroomsController::class, 'markAsReadMessages']);
    Route::patch('/chats/{chatroom:id}/readMessage/{message:id}', [ChatroomsController::class, 'markAsReadMessage']);
    Route::get('/chats/find/{user:id}', [ChatroomsController::class, 'findOrCreateChatroom']);

    Route::get('group-panel/checkForNewMessages', [ChatroomsController::class, 'checkForNewMessages']);
    Route::get('group-panel/getAllUserChatrooms', [ChatroomsController::class, 'getAllUserChatrooms']);


    Route::post('/chats/{chatroom:id}/send', [MessagesController::class, 'store']);

});

Route::get('/invite-member/{token}', [InviteMemberController::class, 'accept'])->name('accept');
Route::post('/invite-member/{token}', [InvitedRegisterController::class, 'store'])->name('registerInvited');

Route::apiResource('/api/users', UserController::class);