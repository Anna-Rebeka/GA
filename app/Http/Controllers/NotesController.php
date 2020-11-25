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

    public function store(Request $fields){
        $attributes = $fields->validate(['text' => ['required', 'max:1000']]);
        $note = Note::create([
            'user_id' => auth()->user()->id,
            'text' => $attributes['text'],
        ]);
        return $note;
    }

    public function destroy($user, int $id) {
        $note = Note::findOrFail($id);
        $note->delete();
    }
}
