<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Note;

class NotesController extends Controller
{
    public function index(User $user){
        return view('profile.notes', [
            'user' => $user
        ]);
    }

    public function store(User $user){
        $attributes = request()->validate(['text' => 'required']);
        Note::create([
            'user_id' => $user->id,
            'text' => $attributes['text'],
        ]);
        return back();
    }

    public function destroy($user, int $id) {
        $note = Note::findOrFail($id);
        $note->delete();
    }
}
