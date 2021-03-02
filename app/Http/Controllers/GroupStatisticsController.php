<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Event;

use Illuminate\Http\Request;



class GroupStatisticsController extends Controller
{
    public function showStatistics(Group $group)
    {
        return view('groups.group-statistics', [
            'user' => auth()->user(),
            'group' => $group,
        ]);
    }

    public function getAssignmentsStatistic(Group $group)
    {
        /*
            SELECT count(*)/sum(select count(*) FROM assignments)
            FROM assignments
            GROUP BY assignment_id

        */
    }
}
