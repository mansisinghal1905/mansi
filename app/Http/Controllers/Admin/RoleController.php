<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\Category;
use DB;
use Hash;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Role;
        $this->middleware('permission:Role-Management', ['only' => ['index','store','create','edit','destroy','update']]);
        $this->columns = [
            "id",
            "role_id",
            "permission_id",
        ];
    }

    public function index()
    {
        $role = Role::all();
    
        return view('admin.roles.index',compact('role'));
    }
    public function getPermissionById($id){
        $permissions =Permission::wherein('id',DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
        ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
        ->all())->pluck('name')->toArray();
         $permissionsStr = "";
         if(isset($permissions)){
            foreach($permissions as $val){
               $permissionsStr.='<span class="badge bg-gray-200 text-dark me-2">'.str_replace("-"," ",$val).'</span><br/>';

            }
         }
         return $permissionsStr;
    }
    public function fetchData($request, $columns) {
        $query =Role::where('name', '!=', '');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }
        $query->where('id',"!=",1);
        if (isset($request['search']['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request['search']['value'] . '%');
            });
        }

        if (isset($request->order_column)) {
            $banners = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $banners = $query->orderBy('created_at', 'desc');
        }
        return $banners;
    }
    public function roleAjax(Request $request)
    {

    $permissions = Role::select(['id', 'name', 'created_at']);

    $request->search = $request->search;
    if (isset($request->order[0]['column'])) {
        $request->order_column = $request->order[0]['column'];
        $request->order_dir = $request->order[0]['dir'];
    }
    $records = $this->fetchData($request, $this->columns);
    $total = $records->get();
    if (isset($request->start)) {
        $banners = $records->offset($request->start)->limit($request->length)->get();
    } else {
        $banners = $records->offset($request->start)->limit(count($total))->get();
    }
    $result = [];


    $i = $request->start;
    foreach ($banners as $value) {
        $data = [];

        $data['srno'] = $i++;
        $data['id'] = $value->id;
        $data['name'] = ucfirst($value->name);
        $data['permissions']=$this->getPermissionById($value->id);
        $data['created_at'] = date('Y-m-d', strtotime($value->created_at)); // Assuming created_at is a Carbon instance
       
        $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

        $action .= '<a href="' . route('admin.roles.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
        
        // $action .= '<a href="' . route('admin.roles.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

        $action.="</div>";

        $data['view'] = $action;

    
        $result[] = $data;
    }

    $data = json_encode([
        'data' => $result,
        'recordsTotal' => count($total),
        'recordsFiltered' => count($total),
    ]);
    return $data;

}

    // public function roleAjax(Request $request)
    // {
    //     $request->search = $request->search;
    //     if (isset($request->order[0]['column'])) {
    //         $request->order_column = $request->order[0]['column'];
    //         $request->order_dir = $request->order[0]['dir'];
    //     }
    //     $records = $this->Model->fetchCategory($request, $this->columns);
    //     $total = $records->get();
    //     if (isset($request->start)) {
    //         $categories = $records->offset($request->start)->limit($request->length)->get();
    //     } else {
    //         $categories = $records->offset($request->start)->limit(count($total))->get();
    //     }
    //     $result = [];
    //     $i = $request->start;
    //     foreach ($categories as $value) {
    //         $data = [];
    //         $data['id'] = ++$i;
    //         $data['role_id'] = $value->role_id;
    //         $data['permission_id'] = $value->permission_id;

    //         // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer roleStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
    //         $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

    //         $action .= '<a href="' . route('admin.roles.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
    //         $action .= '<a href="' . route('admin.roles.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

 
    //         $action.="</div>";

    //         $data['view'] = $action;
    //         $data['status'] = $status;
    //         $result[] = $data;

    //     }
    //     $data = json_encode([
    //         'data' => $result,
    //         'recordsTotal' => count($total),
    //         'recordsFiltered' => count($total),
    //     ]);
    //     return $data;
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = null;
        $permission = Permission::where("id","<>",1)->get();
        // $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
        // ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
        // ->all();

       
        return view('admin.roles.create',compact('role','permission'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
    
        ]);
        $attr = [
            'name' => 'Role Name',
            'permission' => 'Permission Name',
            
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.roles.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $permissionsID = array_map(
                    function($value) { return (int)$value; },
                    $request->input('permission')
                );
            
                $role = Role::create(['name' => $request->input('name')]);
                $role->syncPermissions($permissionsID);

                if ($role->save()) {
                    $request->session()->flash('success', 'Role added successfully');
                    return redirect()->route('admin.roles.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.roles.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.roles.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('admin.roles.view',compact('role','rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {

            $role = Role::find($id);
            
            if (isset($role->id)) {
            
                $type = 'edit';
                
                $permission = Permission::where("id","<>",1)->get();

                $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
                    ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                    ->all();
               
                return view('admin.roles.create', compact('role', 'type','permission','rolePermissions'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.roles.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.roles.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $role = Role::where('id', $id)->first();

            if (isset($role->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
                    'permission' => 'required',
            
                ]);
                $attr = [
                    'name' => 'Role Name',
                    'permission' => 'Permission Name'
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editroles', ['id' => $role->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        
                        $role->name = $request->input('name');
                        
                      
                        if ($role->save()) {

                            $permissionsID = array_map(
                                function($value) { return (int)$value; },
                                $request->input('permission')
                            );
                        
                            $role->syncPermissions($permissionsID);
                           
                            $request->session()->flash('success', 'Role updated successfully');
                            return redirect()->route('admin.roles.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.roles.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.roles.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.roles.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.roles.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function roledestory(Request $request)
    {
        $id = $request->id;
        $record = Role::findOrFail($id); 
        $record->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted Successfully.');
    }

    
}
