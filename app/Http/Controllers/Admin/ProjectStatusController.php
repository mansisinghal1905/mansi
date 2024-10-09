<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\ProjectStatus;
use DB;
use Hash;

class ProjectStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new ProjectStatus;

        $this->columns = [
            "id",
            "name",
            "status",
        ];
    }

    public function index()
    {
        $projectstatus = ProjectStatus::all();
    
        return view('admin.projectstatus.index',compact('projectstatus'));
    }

    public function projectstatusAjax(Request $request)
    {
        // dd($request->all());
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchProjectstatus($request, $this->columns);
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
            $data['name'] = $value->name;
            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer projectstatusStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.projectstatus.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.projectstatus.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

           $action .= '<a href="javascript:void(0)" onclick="deleteProjectstatus(this)" data-url="' . route('admin.projectstatusdestory') . '" class="toolTip deleteProjectstatus" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 
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
        $projectstatus = null;
        
        return view('admin.projectstatus.create',compact('projectstatus'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'name' => 'required',
    
        ]);
        $attr = [
            'name' => 'Project Status Name',
            
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.projectstatus.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $projectstatus = new ProjectStatus;

                $projectstatus->name = $request->name;
                $projectstatus->created_at = date('Y-m-d H:i:s');
                $projectstatus->updated_at = date('Y-m-d H:i:s');
                if ($projectstatus->save()) {
                    $request->session()->flash('success', 'Projectstatus added successfully');
                    return redirect()->route('admin.projectstatus.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.projectstatus.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.projectstatus.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $projectstatus=ProjectStatus::find($id);
        return view('admin.projectstatus.view',compact('projectstatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $projectstatus = ProjectStatus::find($id);
            
            if (isset($projectstatus->id)) {
            
                $type = 'edit';
               
                return view('admin.projectstatus.create', compact('projectstatus', 'type'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.projectstatus.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.projectstatus.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $projectstatus = ProjectStatus::where('id', $id)->first();

            if (isset($projectstatus->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
            
                ]);
                $attr = [
                    'name' => 'Projectstatus Name',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editProjects', ['id' => $projectstatus->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $projectstatus->name = $request->name;
                        $projectstatus->updated_at = date('Y-m-d H:i:s');
                      
                        if ($projectstatus->save()) {
                           
                            $request->session()->flash('success', 'Projectstatus updated successfully');
                            return redirect()->route('admin.projectstatus.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.projectstatus.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.projectstatus.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.projectstatus.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.projectstatus.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function projectstatusdestory(Request $request)
    {
        $id = $request->id;
        $record = ProjectStatus::findOrFail($id);
        $record->delete();
        return redirect()->route('admin.projectstatus.index')->with('success', 'Projectstatus deleted Successfully.');
    }

    public function changeProjectstatusStatus(Request $request)
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
