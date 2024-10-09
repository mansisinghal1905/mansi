<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;


class DashboardController extends Controller
{
    public function dashboard()
    {
        $total_complete_task =Task::where("task_status","complete")->where("status","!=","0")->count();

        $total_task =Task::where("status","!=","0")->count();

        $total_client =User::where("role","3")->where("status","!=","0")->count();

        $total_complete_project =Project::where("project_status","complete")->where("status","!=","0")->count();
        $total_project =Project::where("status","!=","0")->count();

        $total_notstart_project =Project::where("project_status","not_start")->where("status","!=","0")->count();
        $total_inprogress_project =Project::where("project_status","process")->where("status","!=","0")->count();

        
        // dd($total_complete_task);
        return view('admin.index',compact('total_complete_task','total_task','total_client','total_complete_project','total_project','total_notstart_project','total_inprogress_project'));

    }
}
