<?php

namespace App\Http\Controllers;

use App\Models\AssignmentsFile;
use App\Models\Assignment;
use App\Models\User;

use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class AssignmentsFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Assignment $assignment)
    {
        $attributes = $request->validate([
            'assignment_file' => ['file', 'required'],
            'file_name' => ['string','alpha_dash', 'required'],
            ]);
        
        $user = auth()->user();

        $attributes['assignment_file'] = request('assignment_file')->store('assignment_file');
        $ext = pathinfo($attributes['assignment_file'], PATHINFO_EXTENSION);
        
        $attributes['file_name'] .= '.' . $ext;

        $assignmentFile = AssignmentsFile::create([
            'user_id' => $user->id,
            'assignment_file' => $attributes['assignment_file'],
            'file_name' => $attributes['file_name'], 
            'assignment_id' => $assignment->id,
        ]);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AssignmentsFile  $assignmentsFiles
     * @return \Illuminate\Http\Response
     */
    public function show(AssignmentsFile $assignmentsFiles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AssignmentsFile  $assignmentsFiles
     * @return \Illuminate\Http\Response
     */
    public function edit(AssignmentsFile $assignmentsFiles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssignmentsFile  $assignmentsFiles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssignmentsFile $assignmentsFiles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssignmentsFile  $assignmentsFiles
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $assignmentsFile = AssignmentsFile::findOrFail($id);
        Storage::delete($assignmentsFile->assignment_file);
        $assignmentsFile->delete();
        
        return back();
    }
}
