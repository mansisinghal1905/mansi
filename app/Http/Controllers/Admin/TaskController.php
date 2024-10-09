<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\ClientUser;
use App\Models\TaskClient;
use App\Models\User;
use App\Models\TaskUser;
use App\Models\ProjectStatus;
use DB;
use Hash;
use Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Task;

        $this->columns = [
            "id",
            "project_id",
            "developer_id",
        ];
        $this->columns1 = [
            "id",
            "description",
            "project_id",
            "developer_id",
            "task_status",
        ];
    }

    public function index()
    {
        // $task = Task::all();
    
        return view('admin.taskdeveloper.index');
    }

    public function taskAjax(Request $request)
    {
        
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        
        $records = $this->Model->fetchTask($request, $this->columns);
        // dd($records);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i = $request->start;


        $projectData = [];
        // Retrieve the data
        $tasks = $categories;
        // dd($tasks);
        // Filter unique project_id and corresponding values
        foreach($tasks as $task) {
            if (!isset($projectData[$task->project_id])) {
                // If the project_id does not exist in the array, add it
                $projectData[$task->project_id] = [
                    'project_id' => $task->project_id,
                    'developer_id' => $task->developer_id,
                ];
            }
        }
        // dd($projectData);

        foreach ($projectData as $value) {
            $data = [];
            $data['id'] = ++$i;
            // $data['title'] = $value->title;
            // dd($value['project_id']);
            $projectName = Project::where('id', $value['project_id'])->first();
            $developerName = User::where('id', $value['developer_id'])->first();

            // dd($projectName);
            $data['project_id'] = $projectName->title; 

            $data['developer_id'] = $developerName->name ?? '-';
           
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

           
            $url = route('admin.taskshow', [
                'project_id' => $value['project_id'],
                'developer_id' => $value['developer_id']
            ]);

            $action .= '<a href="' . $url . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            
            $action.="</div>";
            
            $data['view'] = $action;
            // $data['status'] = $status;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $task = null;
        // $clientlist = User::where("status","1")->where('id', '!=', 1)->where('role', 3)->get(['id',"name"]);
        $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);

        // dd($clientlist);
        $projectlist = Project::where("status","1")->get(['id',"title"]);
        $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);


        return view('admin.taskdeveloper.create',compact('task','projectlist','developerlist','projectstatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'description' => 'required',
            'project_id' => 'required',
            'hours' => 'required',
            'task_status' => 'required',
    
        ]);
        $attr = [
            'description' => 'Task Description',
            'project_id' => 'project Name',
            'hours' => 'Hours',
            'task_status' => 'Task Status'
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.tasks.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $task = new Task;

                $task->project_id = $request->project_id;
                // $task->user_id = $request->user_id;
                // $task->title =  $request->title;
                $task->task_status = $request->task_status;
                $task->hours = $request->hours;
                $task->description = $request->description;
                $task->developer_id = $request->developer_id;
                // $task->created_at = date('Y-m-d H:i:s');
                // $task->updated_at = date('Y-m-d H:i:s');
                if ($task->save()) {

                    // $user = $request->input('user_id', []); 
                    // $client = $request->input('client_id', []); 

                    // foreach ($user as $key => $user) {
                        
                    //         $userdata = new TaskUser;
                    //         $userdata->task_id = $task->id;
                    //         $userdata->user_id = $user;
                    //         $userdata->save();
                    // }

                    // foreach ($client as $key => $client) {
                        
                    //     $clientdata = new TaskClient;
                    //     $clientdata->task_id = $task->id;
                    //     $clientdata->client_id = $client;
                    //     $clientdata->save();
                    // }
            
                    // Attach the selected users to the task
                    // $task->taskusers()->attach($request->user_id);
                    // $task->taskclients()->attach($request->client_id);


                    $request->session()->flash('success', 'Task added successfully');
                    return redirect()->route('admin.tasks.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.tasks.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.tasks.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function taskshow($projectid,$developerid)
    {
        // dd($developerid);
        // $task=Task::where(['project_id'=>$projectid,'developer_id'=>$developerid])->get();
        $task = Task::where('project_id', $projectid)
        ->where('developer_id', $developerid)
        ->get();
        
        // dd($task);
        return view('admin.taskdeveloper.view',compact('task','projectid','developerid'));
    }

    public function ShowtaskAjax(Request $request)
    {
        // dd($request->all());
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
         // Fetch project statuses
        $projectStatuses = ProjectStatus::all();
        $records = $this->Model->fetchshowTask($request, $this->columns1);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i = $request->start;
        foreach ($categories as $value) {
            $data = [];
            $data['id'] = ++$i;
            // $data['title'] = $value->title;
            $data['project_id'] = $value->getProject->title;
            $data['developer_id'] = $value->getDeveloper->name ?? '-';

            $data['description'] = $value->description;
            $data['hours'] = $value->hours;
            $data['task_status'] = ucfirst($value->task_status);

            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));


            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer clientuserStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
            // if(Auth::user()->role == 2){
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';
            
            $action .= '<a href="' . route('admin.tasks.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            // $action .= '<a href="' . route('admin.tasks.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            // $action .= '<a href="javascript:void(0)" onclick="deleteTasks(this)" data-url="' . route('admin.taskdestory') . '" class="toolTip deleteTasks" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            $action.="</div>";
        
            if(Auth::user()->role == 2){
            $data['view'] = $action;
            }else{
                $data['view'] = 'N/A';
            }

            // $data['view'] = $action;
        // }
            // $data['status'] = $status;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            // $task = Task::where('id', $id)->first();
            $task = Task::find($id);
            // dd($task);
            
            if (isset($task->id)) {
            
                $type = 'edit';
               
                // $clientlist = User::where("status","1")->where('id', '!=', 1)->get(['id',"name"]);
                
                $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);

                // Fetch selected client IDs for the task (assuming many-to-many relationship)
                // $selectedClientIds = $task->getclienttask->pluck('client_id')->toArray();
            
                $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);
                // dd($developerlist);

                // Fetch selected clientuser IDs for the task (assuming many-to-many relationship)
                // $selectedClientUserIds = $task->getusertask->pluck('user_id')->toArray();
            
                $projectlist = Project::where("status","1")->get(['id',"title"]);
            
                return view('admin.taskdeveloper.create', compact('task', 'type','developerlist','projectlist','projectstatus'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.tasks.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.tasks.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $task = Task::where('id', $id)->first();

            if (isset($task->id)) {
                $validate = Validator($request->all(),  [
                    'description' => 'required',
                    'project_id' => 'required',
                    'hours' => 'required',
                    'task_status' => 'required',
                ]);
                $attr = [
                    'description' => 'Description',
                    'project_id' => 'project Name',
                    'hours' => 'Hours',
                    'task_status' => 'Task Status',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('admin.tasks.edit', ['id' => $task->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $task->project_id = $request->project_id;
                        $task->developer_id = $request->developer_id;
                        $task->task_status = $request->task_status;
                        $task->hours = $request->hours;
                        $task->description = $request->description;
                        $task->updated_at = date('Y-m-d H:i:s');
                        
                        // dd($task);
                        if ($task->save()) {

                            // $user = $request->input('user_id', []); 
                            // $client = $request->input('client_id', []); 
        
                            // foreach ($user as $key => $user) {
                                
                            //         $userdata = new TaskUser;
                            //         $userdata->task_id = $task->id;
                            //         $userdata->user_id = $user;
                            //         $userdata->save();
                            // }
        
                            // foreach ($client as $key => $client) {
                                
                            //     $clientdata = new TaskClient;
                            //     $clientdata->task_id = $task->id;
                            //     $clientdata->client_id = $client;
                            //     $clientdata->save();
                            // }

                             // Delete existing user and client relationships
                            // TaskUser::where('task_id', $task->id)->delete();
                            // TaskClient::where('task_id', $task->id)->delete();

                            // Re-insert the new user relationships
                            // foreach ($request->input('user_id', []) as $userId) {
                            //     TaskUser::create([
                            //         'task_id' => $task->id,
                            //         'user_id' => $userId
                            //     ]);
                            // }

                            // Re-insert the new client relationships
                            // foreach ($request->input('client_id', []) as $clientId) {
                            //     TaskClient::create([
                            //         'task_id' => $task->id,
                            //         'client_id' => $clientId
                            //     ]);
                            // }

                            $request->session()->flash('success', 'Task updated successfully');
                            return redirect()->route('admin.tasks.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.tasks.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.tasks.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.tasks.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.tasks.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function taskdestory(Request $request)
    {
        $id = $request->id;
        $record = Task::findOrFail($id);
        $record->status = 2; 
        $record->save();
        return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully.');;
    }

    // public function ChangeTaskStatus(Request $request)
    // {
    //     $response = $this->Model->where('id', $request->id)->update(['status' => $request->status]);
    //     if ($response) {
    //         return json_encode([
    //             'status' => true,
    //             "message" => "Status Changes Successfully"
    //         ]);
    //     } else {
    //         return json_encode([
    //             'status' => false,
    //             "message" => "Status Changes Fails"
    //         ]);
    //     }
    // }

    public function updateProjectStatus(Request $request)
    {
        $response = $this->Model->where('id', $request->id)->update(['project_status' => $request->project_status]);
       
        if ($response) {
            return json_encode([
                'status' => true,
                "message" => "Status Changes Successfully"
            ]);
        } else {
            return json_encode([
                'status' => false,
                "message" => "Status Changes Fails"
            ]);
        }
      
    }
}
