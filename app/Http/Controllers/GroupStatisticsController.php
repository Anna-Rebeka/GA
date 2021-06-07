<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Event;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class GroupStatisticsController extends Controller
{
    public function showStatistics()
    {
        $group = Group::find(auth()->user()->active_group);

        if($group->admin_id != auth()->user()->id){
            Abort(401);
        }

        $users_with_assignments = DB::table('assignment_user')
            ->select('assignment_user.user_id')
            ->distinct()
            ->join('assignments', 'assignment_user.assignment_id', '=', 'assignments.id')
            ->where('assignments.group_id', '=', $group->id)
            ->get();


        $users_with_assignments = $users_with_assignments->pluck('user_id');
        $users_with_no_assignments = $group->users->whereNotIn('id', $users_with_assignments)->flatten()->all();
        return view('groups.group-statistics', [
            'user' => auth()->user(),
            'group' => $group,
            'stats' => $this->getAssignmentsStatistic($group),
            'free_users' => $users_with_no_assignments,
        ]);
    }

    public function getAssignmentsStatistic(Group $group)
    {
        $all_assignments = Assignment::where('group_id', $group->id)->count();
        $assignments_per_user = DB::table('assignments')
            ->select(DB::raw('assignment_user.user_id as id, users.name, users.username, users.avatar, count(*) / ' . $all_assignments . ' as user_to_all'))
            ->join('assignment_user', 'assignment_user.assignment_id', '=', 'assignments.id')
            ->join('users', 'assignment_user.user_id', '=', 'users.id')
            ->where('assignments.group_id', '=', $group->id)
            ->groupBy('assignment_user.user_id', 'users.name', 'users.username', 'users.avatar')
            ->orderBy('user_to_all')
            ->get();
        
        return $assignments_per_user->toArray();
    }
}
