<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssign;
use App\Models\ProjectStatus;
use App\Models\TaskAssign;
use App\Models\Notification;
use DB;
use Hash;
use Auth;

class ProjectAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new ProjectAssign;
        $this->middleware('permission:Project-Assign-Management', ['only' => ['index','store','create','edit','destroy','update']]);

        $this->columns = [
            "id",
            "project_id",
            "developer_id",
            "project_status",
            "priority"
        ];
    }

    public function index()
    {
        $projectassign = ProjectAssign::all();
        // dd($projectassign);
    
        return view('admin.projectassign.index',compact('projectassign'));
    }

    public function projectassignAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
       
        $records = $this->Model->fetchProjectAssign($request, $this->columns);
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
            $data['developer_id'] = $value->getDeveloper->name;
            $data['project_id'] = $value->getProject->title;
    
            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer projectassignStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";

            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer projectassignStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";

        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.projects-assign.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.projects-assign.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
            if(Auth::user()->role == 1){
            $action .= '<a href="javascript:void(0)" onclick="deleteProjectAssigns(this)" data-url="' . route('admin.projectassigndestory') . '" class="toolTip deleteProjectAssigns" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
            
            // New "Assign Task" button
            // $action .= '<a href="' . route('admin.createtask', $value->id) . '" class="toolTip btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Assign Task">Task Assign</a>';
            }
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projectassign = null;
        $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);
        $projectlist = Project::where("status","1")->get(['id',"title"]);
        $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);

        return view('admin.projectassign.create',compact('projectassign','projectlist','developerlist','projectstatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'project_id' => 'required',
            'developer_id' => 'required',
    
        ]);
        $attr = [
            'project_id' => 'project Name',
            'developer_id' => 'Developer Name',
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.projects-assign.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $projectassign = new ProjectAssign;

                $projectassign->project_id = $request->project_id;
                $projectassign->developer_id = $request->developer_id;
                $projectassign->project_status = $request->project_status;
                $projectassign->priority = $request->priority;
                $projectassign->created_at = date('Y-m-d H:i:s');
                $projectassign->updated_at = date('Y-m-d H:i:s');
                if ($projectassign->save()) {

                $getProjectName = Project::where("id",$request->project_id)->first();

                // Save the notification for the developer
                $notification = new Notification;
                $notification->user_id = $request->developer_id;
                $notification->project_id = $request->project_id;
                $notification->title = "New Project Assignment";
                $notification->message = "You have been assigned to project: " . $getProjectName->title;
                $notification->created_at = now();
                $notification->updated_at = now();
                $notification->save();

                    $request->session()->flash('success', 'Project Assign added successfully');
                    return redirect()->route('admin.projects-assign.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.projects-assign.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.projects-assign.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $projectassign = ProjectAssign::find($id);
        // dd($projectassign);
        $taskassigns = TaskAssign::where('project_id', $projectassign->id)
                                    ->where('developer_id', $projectassign->developer_id)->get();
        // dd($taskassigns);
       
        return view('admin.projectassign.view',compact('projectassign','taskassigns'));
    }

    /**
     * Show the form for editing the specified resource.
    */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $projectassign = ProjectAssign::find($id);
            // dd($projectassign);
            
            if (isset($projectassign->id)) {
            
                $type = 'edit';

                $developerlist = User::where("status","1")->where('id', '!=', 1)->where('role', 2)->get(['id',"name"]);
                // dd($developerlist);
            
                $projectlist = Project::where("status","1")->get(['id',"title"]);
                $projectstatus = ProjectStatus::where("status","1")->get(['id',"name"]);
            
                return view('admin.projectassign.create', compact('projectassign', 'type','developerlist','projectlist','projectstatus'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.projects-assign.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.projects-assign.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $projectassign = ProjectAssign::where('id', $id)->first();

            if (isset($projectassign->id)) {
                $validate = Validator($request->all(),  [
                    'project_id' => 'required',
                    'developer_id' => 'required',
                ]);
                $attr = [
                    'project_id' => 'project Name',
                    'developer_id' => 'Developer Name',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('admin.projects-assign.edit', ['id' => $projectassign->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $projectassign->project_id = $request->project_id;
                        $projectassign->developer_id = $request->developer_id;
                        $projectassign->project_status = $request->project_status;
                        $projectassign->priority = $request->priority;
                        $projectassign->updated_at = date('Y-m-d H:i:s');
                      
                        if ($projectassign->save()) {

                            $getProjectName = Project::where("id",$projectassign->project_id)->first();

                            $notification = Notification::where(['project_id'=>$request->project_id, 'user_id'=>$request->developer_id])
                            ->delete();

                            // Save the notification for the developer
                            $notification = new Notification;
                            $notification->user_id = $request->developer_id;
                            $notification->project_id = $request->project_id;
                            $notification->title = "New Project Assignment";
                            $notification->message = "You have been assigned to project: " . $getProjectName->title;
                            $notification->created_at = now();
                            $notification->updated_at = now();
                            $notification->save();
            
            
                            $request->session()->flash('success', 'Project Assign updated successfully');
                            return redirect()->route('admin.projects-assign.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.projects-assign.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.projects-assign.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.projects-assign.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.projects-assign.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function projectassigndestory(Request $request)
    {
        $id = $request->id;
        $record = ProjectAssign::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.projects-assign.index')->with('success', 'Project Assign deleted successfully.');;
    }


    public function changeProjectAssignStatus(Request $request)
    {
        $response = $this->Model->where('id', $request->id)->update(['status' => $request->status]);
       
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
