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
    public function showStatistics(Group $group)
    {
        return view('groups.group-statistics', [
            'user' => auth()->user(),
            'group' => $group,
            'stats' => $this->getAssignmentsStatistic($group),
        ]);
    }

    public function getAssignmentsStatistic(Group $group)
    {
        $all_assignments = Assignment::count();
        $assignments_per_user = DB::table('assignments')
             ->select(DB::raw('assignment_user.user_id, users.name, users.username, users.avatar, count(*) / ' . $all_assignments . ' as user_to_all'))
             ->join('assignment_user', 'assignment_user.assignment_id', '=', 'assignments.id')
             ->join('users', 'assignment_user.user_id', '=', 'users.id')
             ->groupBy('assignment_user.user_id', 'users.name', 'users.username', 'users.avatar')
             ->orderBy('user_to_all')
             ->get();

        return $assignments_per_user;

    }
}
