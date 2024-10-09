<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\TaskAssign;
use App\Models\User;
use App\Models\ProjectStatus;
use App\Models\ProjectAssign;
use Carbon\Carbon;
use DB;
use Hash;

class TaskAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new TaskAssign;
        // $this->middleware('permission:Task-Assign-Management', ['only' => ['index','store','create','edit','destroy','update']]);

        $this->columns = [
            "id",
            "title",
            "project_id",
        
            "status"

        ];
    }

    public function index()
    {
        $taskassign = TaskAssign::all();
    
        return view('admin.tasks.index',compact('taskassign'));
    }

    public function taskassignAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
         // Fetch project statuses
        // $projectStatuses = ProjectStatus::all();
        $records = $this->Model->fetchTask($request, $this->columns);
        $total = $records->get();
        if (isset($request->start)) {
            $categories = $records->offset($request->start)->limit($request->length)->get();
        } else {
            $categories = $records->offset($request->start)->limit(count($total))->get();
        }
        $result = [];
        $i = $request->start;

        // dd($categories);
        foreach ($categories as $value) {
            $data = [];
            $data['id'] = ++$i;
            $data['task_title'] = $value->task_title;
            $data['project_id'] = $value->getProject->title??'N/A';
            $data['developer_id'] = $value->getDeveloper->name??'N/A';
            $data['description'] = Str::limit($value->description, 20);
            $data['task_status'] = ucfirst($value->task_status ?? 'N/A');
            $data['hours'] = $value->hours ?? 'N/A';
            $data['start_date'] = $value->start_date ?? 'N/A';
            $data['end_date'] = $value->end_date ?? 'N/A';

            // $data['task_status'] = '<select class="form-control status-select" data-id="' . $value->id . '" data-select2-selector="status">';
            // $data['task_status'] .= '<option value="pending" data-bg="bg-success"' . ($value->task_status == 'pending' ? ' selected' : '') . '>Pending</option>';
            // $data['task_status'] .= '<option value="progress" data-bg="bg-warning"' . ($value->task_status == 'progress' ? ' selected' : '') . '>Progress</option>';
            // $data['task_status'] .= '<option value="complete" data-bg="bg-danger"' . ($value->task_status == 'complete' ? ' selected' : '') . '>Complete</option>';
            
            // $data['task_status'] .= '</select>';
    
            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer taskStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.taskassign.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.taskassign.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            // $action .= '<a href="javascript:void(0)" onclick="deleteTasks(this)" data-url="' . route('admin.taskdestory') . '" class="toolTip deleteTasks" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            $action.="</div>";

            $data['view'] = $action;
            $data['status'] = $status;
            $result[] = $data;

        }
        $data = json_encode([
            'data' => $result,
            'recordsTotal' => count($total),
            'recordsFiltered' => count($total),
        ]);
        return $data;
    }
    // public function create()
    // {

    // }

    /**
     * Show the form for creating a new resource.
     */
    public function createtask($project_assign_id)
    {
        
        $taskassign = null;
        $developerid = ProjectAssign::where('id',$project_assign_id)->select('developer_id')->first();
        // dd($developerid);
        $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);

        // dd($clientlist);
        $projectlist = Project::where("status","1")->get(['id',"title"]);
        $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);

         return view('admin.tasks.create',compact('taskassign','projectlist','developerlist','projectstatus','project_assign_id','developerid'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
        // dd($input);
      
        $validate = Validator($request->all(), [
            'task_title' => 'required',
            'description' => 'required',
            
    
        ]);
        $attr = [
            'task_title' => 'Title',
            'description' => 'Description',
            
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->back()->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $taskassign = new TaskAssign;

                $taskassign->project_id = $request->project_id;
                $taskassign->developer_id = $request->developer_id;
                $taskassign->task_title =  $request->task_title;
                $taskassign->description = $request->description;
                $taskassign->start_date = $request->start_date;
                $taskassign->end_date = $request->end_date;
                $taskassign->task_status = $request->task_status;
                // $taskassign->created_at = date('Y-m-d H:i:s');
                // $taskassign->updated_at = date('Y-m-d H:i:s');
                if ($taskassign->save()) {

                    $request->session()->flash('success', 'Task added successfully');
                    return redirect()->route('admin.taskassign.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.taskassign.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.taskassign.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $taskassign=TaskAssign::find($id);
    
        return view('admin.tasks.view',compact('taskassign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            // $taskassign = TaskAssign::where('id', $id)->first();
            $taskassign = TaskAssign::find($id);
            // dd($taskassign);
            
            if (isset($taskassign->id)) {
            
                $type = 'edit';
               
                
                // $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);

                // Fetch selected client IDs for the task (assuming many-to-many relationship)
                // $selectedClientIds = $taskassign->getclienttask->pluck('client_id')->toArray();
            
                $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);
                // dd($developerlist);

                // Fetch selected clientuser IDs for the task (assuming many-to-many relationship)
                // $selectedClientUserIds = $taskassign->getusertask->pluck('user_id')->toArray();
            
                $projectlist = Project::where("status","1")->get(['id',"title"]);
            
                return view('admin.tasks.create', compact('taskassign', 'type','developerlist','projectlist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.taskassign.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.taskassign.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $taskassign = TaskAssign::where('id', $id)->first();
            // dd($taskassign);
            if (isset($taskassign->id)) {
                $validate = Validator($request->all(), [
                    'task_title' => 'required',
                    'description' => 'required',
                    // 'task_status' => 'required',
                ]);
                $attr = [
                    'task_title' => 'Title',
                    'description' => 'Description',
                    // 'task_status' => 'Task Status',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('admin.taskassign.edit', ['id' => $taskassign->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $taskassign->project_id = $request->project_id;
                        $taskassign->developer_id = $request->developer_id;
                        $taskassign->task_title =  $request->task_title;
                        $taskassign->description = $request->description;
                        $taskassign->start_date = $request->start_date;
                        $taskassign->end_date = $request->end_date;
                        $taskassign->task_status = $request->task_status;
                        $taskassign->updated_at = date('Y-m-d H:i:s');
                        
                        // dd($taskassign);
                        if($request->task_status == 'complete'){
                            $start_date = Carbon::parse($request->start_date);  // Example start date-time
                            $end_date = Carbon::parse($request->end_date); 

                            $minutesDifference = $start_date->diffInMinutes($end_date);
                            $hoursDifference = $minutesDifference / 60;

                            $taskassign->hours = number_format($hoursDifference, 2); // Format to 2 decimal places
                        }
                        // dd($taskassign);
                        // echo "Difference in hours: " . $hoursDifference;
                        if ($taskassign->save()) {

                            $request->session()->flash('success', 'Task updated successfully');
                            return redirect()->route('admin.taskassign.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.taskassign.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.taskassign.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.taskassign.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.taskassign.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function taskdestory(Request $request)
    {
        $id = $request->id;
        $record = TaskAssign::findOrFail($id);
        $record->status = 2; 
        $record->save();
        return redirect()->route('admin.taskassign.index')->with('success', 'Task deleted successfully.');;
    }

   
    public function changeTaskAssignStatus(Request $request)
    {
       $getRecord =  $this->Model->where('id', $request->id)->first();
        if($getRecord){
            $getRecord->status = $request->status;
            $getRecord->save();
            return json_encode([
                'status' => true,
                "message" => "Status Changes Successfully"
            ]);
        }
 else {
            return json_encode([
                'status' => false,
                "message" => "No Record found"
            ]);
        }
      
    }

    public function TaskStatus(Request $request)
    {
        $response = $this->Model->where('id', $request->id)->update(['task_status' => $request->task_status]);
        // dd($response);
        if ($response) {
            return json_encode([
                'status' => true,
                "message" => "Task Status Changes Successfully"
            ]);
        } else {
            return json_encode([
                'status' => false,
                "message" => "Status Changes Fails"
            ]);
        }
    }
}
