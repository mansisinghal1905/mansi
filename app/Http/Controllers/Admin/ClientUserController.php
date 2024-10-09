<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\ClientUser;
use App\Models\User;
use DB;
use Hash;

class ClientUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new ClientUser;

        $this->columns = [
            "id",
            "title",
            "client",
            "start_date",
            "end_date",
            "status",

        ];
    }

    public function index()
    {
        $clientuser = ClientUser::all();
    
        return view('admin.clientusers.index',compact('clientuser'));
    }

    public function clientuserAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchClientUser($request, $this->columns);
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
            $data['client_id'] = $value->getCLient->name;
            $data['fullname'] = ucfirst($value->first_name . ' ' . $value->last_name);
            $data['email'] = $value->email;
            $data['phone'] = $value->phone;
            // $data['position'] = ucfirst($value->position);
            // $data['timezone'] = $value->timezone;
            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer clientuserStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.clientusers.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.clientusers.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteClientUsers(this)" data-url="' . route('admin.clientuserdestory') . '" class="toolTip deleteClientUsers" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
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
        $clientuser = null;
        $clientlist = User::where("status","1")->where('id', '!=', 1)->get(['id',"name"]);
        // dd($clientlist);
        return view('admin.clientusers.create',compact('clientuser','clientlist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request) {
        $input = $request->all();
      
        $validate = Validator($request->all(), [
            'client_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
    
        ]);
        $attr = [
            'client_id' => 'Client Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email'
        ];
        $validate->setAttributeNames($attr);
        if ($validate->fails()) {
            return redirect()->route('admin.clientusers.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $clientuser = new ClientUser;

                $clientuser->client_id = $request->client_id;
                $clientuser->first_name =  $request->first_name;
                $clientuser->last_name = $request->last_name;
                $clientuser->email = $request->email;
                $clientuser->phone = $request->phone;
                $clientuser->position = $request->position;
                $clientuser->created_at = date('Y-m-d H:i:s');
                $clientuser->updated_at = date('Y-m-d H:i:s');
                if ($clientuser->save()) {
                    $request->session()->flash('success', 'Client User added successfully');
                    return redirect()->route('admin.clientusers.index');
                } else {
                    $request->session()->flash('error', 'Something went wrong. Please try again later.');
                    return redirect()->route('admin.clientusers.index');
                }
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.clientusers.index');
            }

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $clientuser=ClientUser::find($id);
        return view('admin.clientusers.view',compact('clientuser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            // $clientuser = ClientUser::where('id', $id)->first();
            $clientuser = ClientUser::find($id);
            // dd($clientuser);
            
            if (isset($clientuser->id)) {
            
                $type = 'edit';
               
                 $clientlist = User::where("status","1")->where('id', '!=', 1)->get(['id',"name"]);
                    // dd($clientlist);
                return view('admin.clientusers.create', compact('clientuser', 'type','clientlist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.clientusers.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.clientusers.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $clientuser = ClientUser::where('id', $id)->first();

            if (isset($clientuser->id)) {
                $validate = Validator($request->all(),  [
                    'client_id' => 'required',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required',
                ]);
                $attr = [
                    'title' => 'Title',
                    'client' => 'Client Name', 'client_id' => 'Client Name',
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'email' => 'Email'
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editclientusers', ['id' => $clientuser->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $clientuser->client_id = $request->client_id;
                        $clientuser->first_name =  $request->first_name;
                        $clientuser->last_name = $request->last_name;
                        $clientuser->email = $request->email;
                        $clientuser->phone = $request->phone;
                        $clientuser->position = $request->position;
                        $clientuser->updated_at = date('Y-m-d H:i:s');
                        
                        // dd($clientuser);
                        if ($clientuser->save()) {
                            // DB::table('model_has_roles')->where('model_id',$id)->delete();
                            //$clientuser->assignRole($request->post('roles'));
                            $request->session()->flash('success', 'Client User updated successfully');
                            return redirect()->route('admin.clientusers.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.clientusers.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.clientusers.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.clientusers.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.clientusers.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function clientuserdestory(Request $request)
    {
        $id = $request->id;
        $record = ClientUser::findOrFail($id);
        $record->status = 2; 
        $record->save();
        return redirect()->route('admin.clientusers.index')->with('success', 'Client User deleted successfully.');;
    }

    public function ChangeClientUserStatus(Request $request)
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
