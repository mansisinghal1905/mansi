<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Category;
use App\Models\BankInformation;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Mail\UserDetailsMail;
use DB;
use Mail;
use Hash;

class DeveloperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new User;
        $this->columns = [
            "id",
            "name",
            "email",
            "phone_number",
            "designation",
            "status",
            "login_time",
            "logout_time"
        ];
    }

     public static function generateReferralCode($developer_id) {
		
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$code = substr($letters, mt_rand(0, 24), 2) . mt_rand(1000, 9999) . substr($letters, mt_rand(0, 23), 3) . mt_rand(10, 99).$developer_id;

		return $code;
	}

    public function index()
    {
        $developer = User::all();
        return view('admin.developers.index',compact('developer'));
    }

    public function developerAjax(Request $request)
    {
        
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchDeveloper($request, $this->columns);
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
            $data['email'] = $value->email;
            $data['phone_number'] = $value->phone_number;
            $data['designation'] = $value->Designation->name;
            $data['avatar'] = ($value->avatar != null) ? '<img src="'. $value->avatar.'" height="40%"width="40%" />' : '-';
            // $data['login_time'] = $value->login_time ? date('Y-m-d', strtotime($value->login_time)) : 'N/A';
            $data['login_time'] = $value->login_time ?? 'N/A';
            $data['logout_time'] = $value->logout_time ?? 'N/A';

            // $data['logout_time'] = $value->logout_time ? date('Y-m-d', strtotime($value->logout_time)) : 'N/A';

           
            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer developerStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            
            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.developers.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.developers.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

            $action .= '<a href="javascript:void(0)" onclick="deleteDevelopers(this)" data-url="' . route('admin.developerdestory') . '" class="toolTip deleteDevelopers" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 

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
        $developer = null;
        $categorylist = Category::where("status","1")->get(['id',"name"]);

        return view('admin.developers.create',compact('developer','categorylist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
   

    public function store(Request $request) {
        $input = $request->all();
        // dd($input);
        $validate = Validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
            'avatar' => 'required',

        ]);
    
        $attr = [
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone no',
            'avatar' => 'Image',

        ];
        $validate->setAttributeNames($attr);
        // dd($validate);
        if ($validate->fails()) {
            return redirect()->route('admin.developers.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $checkedPhone = User::where("phone_number", $request->phone_number)
                                    ->where("status", "!=", "2")
                                    ->first();
                if ($checkedPhone) {
                    $request->session()->flash('error', 'Phone number already exists');
                    return redirect()->back();
                }

                // $roles = Role::where("name",$request->role)->first();

                $developer = new User;
                $filename = "";
                if ($request->hasfile('avatar')) {
                    $file = $request->file('avatar');
                    $filename = time() . $file->getClientOriginalName();
                    $filename = str_replace(' ', '', $filename);
                    $filename = str_replace('.jpeg', '.jpg', $filename);
                    $file->move(public_path('profileimage'), $filename);
                }
                if ($filename != "") {
                    $developer->avatar = $filename;
                }
                $password = $this->generateReferralCode(1);
    
                $developer->name = ucfirst($request->name);
                $developer->email = $request->email;
                $developer->phone_number = $request->phone_number;
                $developer->password = Hash::make($password);
                $developer->designation = $request->designation;
                $developer->role = 2;
                // dd($developer);
                $developer->save();
               $getrole =  Role::where("id",2)->first();
             //   $developer->assignRole($getrole->name);
                $developer->assignRole([$developer->role]);

                // Send user details via email
                Mail::to($developer->email)->send(new UserDetailsMail($developer, $password));

                
                $request->session()->flash('success', 'Developer added successfully');
                return redirect()->route('admin.developers.index');
            } catch (Exception $e) {
                dd($e);
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.developers.index');
            }
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $developer=User::find($id);
        return view('admin.developers.view',compact('developer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            $developer = User::find($id);
            
            if (isset($developer->id)) {
            
                $type = 'edit';
                $categorylist = Category::where("status","1")->get(['id',"name"]);

                return view('admin.developers.create', compact('developer', 'type','categorylist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.developers.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.developers.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $developer = User::where('id', $id)->first();
            $checkedMail = User::where("id","!=",$id)->where("email",$request->email)->first();
            if($checkedMail)
            {
                 $request->session()->flash('error', 'Email address already exists');
                 return redirect()->back();
            }
             $checkedPhone = User::where("status","!=","delete")->where("phone_number",$request->phone_number)->where("id","!=",$id)->first();
                if($checkedPhone)
                {
                    $request->session()->flash('error', 'Phone number already exists');
                    return redirect()->back();
                }

            if (isset($developer->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone_number' => 'required|min:8|numeric',
                ]);
                $attr = [
                    'name' => 'FUll Name',
                    'email' => 'Email',
                    'phone_number' => 'Mobile',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editDevelopers', ['id' => $developer->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        if ($request->hasFile('avatar')) {
                            $file = $request->file('avatar');
                            $filename = time() . '_' . str_replace(' ', '', $file->getClientOriginalName());
                            $filename = str_replace('.jpeg', '.jpg', $filename);
                            $file->move(public_path('profileimage'), $filename);
        
                            if ($developer->avatar && file_exists(public_path('profileimage/' . $developer->avatar)) && $developer->avatar != 'noimage.jpg') {
                                unlink(public_path('profileimage/' . $developer->avatar));
                            }
        
                            $developer->avatar = $filename;
                        }
                        $developer->name = ucfirst($request->name);
                        $developer->email = $request->email;
                        $developer->phone_number = $request->phone_number;
                        $developer->designation = $request->designation;
                      
                        $developer->role = 2;
                        $developer->updated_at = date('Y-m-d H:i:s');
                        
                        // dd($developer);
                        if ($developer->save()) {
                            $developer->assignRole([$developer->role]);

                            // DB::table('model_has_roles')->where('model_id',$id)->delete();
                            //$developer->assignRole($request->post('roles'));
                            $request->session()->flash('success', 'Developer updated successfully');
                            return redirect()->route('admin.developers.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.developers.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.developers.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.developers.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.developers.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function developerdestory(Request $request)
    {
        $id = $request->id;
        $record = User::findOrFail($id); 
        $record->delete();

        return redirect()->route('admin.developers.index')->with('success', 'Developer deleted Successfully.');;
   
    }

    public function ChangeDeveloperStatus(Request $request)
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
