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
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use DB;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new User;
        $this->middleware('permission:Customer-Management', ['only' => ['index','store','create','edit','destroy','update']]);

        $this->columns = [
            "id",
            "name",
            "email",
            "phone_number",
            "status",

        ];
    }

     public static function generateReferralCode($user_id) {
		
		$letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$code = substr($letters, mt_rand(0, 24), 2) . mt_rand(1000, 9999) . substr($letters, mt_rand(0, 23), 3) . mt_rand(10, 99).$user_id;

		return $code;
	}
    public function index()
    {
        $user = User::all();
        
        return view('admin.users.index',compact('user'));
    }

    public function userAjax(Request $request)
    {
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
        $records = $this->Model->fetchUser($request, $this->columns);
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
           
            $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer userStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
        
            // $view = "<a href='" . route('admin.users.show', $value->id) . "' data-status='1' class='badge badge-secondary userStatus'>View</a>";

            $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

            $action .= '<a href="' . route('admin.users.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
            $action .= '<a href="' . route('admin.users.show', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

         
            
            $action .= '<a href="javascript:void(0)" onclick="deleteUsers(this)" data-url="' . route('admin.userdestory') . '" class="toolTip deleteUsers" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
 

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
        $user = null;
        $countrylist = Country::where("status","active")->get(['id',"name"]);
        $statelist = null;//State::where("status",'active')->get();
		$citylist = null;//City::where("status",'active')->get();
        $categorylist = Category::where("status","1")->get(['id',"name"]);

        return view('admin.users.create',compact('countrylist','statelist','citylist','user','categorylist'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
   

    public function store(Request $request) {
        $input = $request->all();
        
        $validate = Validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
        ]);
    
        $attr = [
            'name' => 'Name',
            'email' => 'Email',
            'phone_number' => 'Phone no',
        ];
        $validate->setAttributeNames($attr);
    
        if ($validate->fails()) {
            return redirect()->route('admin.users.create')->withInput($request->all())->withErrors($validate);
        } else {
            try {
                $checkedPhone = User::where("phone_number", $request->phone_number)
                                    ->where("status", "!=", "delete")
                                    ->first();
                if ($checkedPhone) {
                    $request->session()->flash('error', 'Phone number already exists');
                    return redirect()->back();
                }

                // $roles = Role::where("name",$request->role)->first();

                $user = new User;
                $password = $this->generateReferralCode(1);
    
                $user->name = ucfirst($request->name);
                $user->company_name = $request->company_name;
                $user->email = $request->email;
                $user->phone_number = $request->phone_number;
                $user->password = Hash::make($password);
                $user->category = $request->category;
                $user->country_id = $request->country_id;
                $user->state_id = $request->state_id;
                $user->city_id = $request->city_id;
                $user->zip_code = $request->zip_code;
                $user->website = $request->website;
                $user->address = $request->address;
                $user->role = 3;

                $user->save();
    
                // Create the bank information entry
                BankInformation::create([
                    'user_id' => $user->id,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'account_holder_name' => $request->account_holder_name,
                    'ifsc_code' => $request->ifsc_code,
                    'branch_name' => $request->branch_name,
                    'bank_address' => $request->bank_address,
                ]);
                
                $request->session()->flash('success', 'Client added successfully');
                return redirect()->route('admin.users.index');
            } catch (Exception $e) {
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.users.index');
            }
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user=User::with('bankinfo')->find($id);
        // dd($user);
        return view('admin.users.view',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id = null) {
        if (isset($id) && $id != null) {
            // $user = User::where('id', $id)->first();
            $user = User::find($id);
            $CountryCode =Country::select('shortname')->first();
            
            if (isset($user->id)) {
                $user->country_flag = $CountryCode->shortname ??'IL';
            
                $type = 'edit';
                // $url = route('admin.users.update', ['id' => $user->id]);
                // $roles = Role::pluck('name','name')->all();
                // $userRole = $user->roles->pluck('name','name')->all();
                // Fetch related bank information
                $bankInformation = BankInformation::where('user_id', $id)->first();
               
                 $countrylist = Country::where("status","active")->get(['id',"name"]);
                 $statelist = State::where("status","active")->where("country_id",$user->country_id)->get(['id',"name"]);
                 $cityList = City::where("state_id",$user->state_id)->get(['id',"name"]);
                 $categorylist = Category::where("status","1")->get(['id',"name"]);

                return view('admin.users.create', compact('user', 'type','countrylist','statelist','cityList','bankInformation','categorylist'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.users.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.users.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $user = User::where('id', $id)->first();
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

            if (isset($user->id)) {
                $validate = Validator($request->all(),  [
                    'name' => 'required',
                    'email' => 'required|email',
                    'phone_number' => 'required|min:8|numeric',
                ]);
                $attr = [
                    'name' => 'FUll Name',
                    'user_email' => 'Email',
                    'phone_number' => 'Mobile',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('editUsers', ['id' => $user->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                        $user->name = ucfirst($request->name);
                        $user->company_name =  $request->company_name;
                        $user->email = $request->email;
                        $user->phone_number = $request->phone_number;
                        $user->category = $request->category;
                        $user->country_id = $request->country_id;
                        $user->state_id = $request->state_id;
                        $user->city_id = $request->city_id;
                        $user->zip_code = $request->zip_code;
                        $user->website = $request->website;
                        $user->address = $request->address;
                        $user->role = 3;
                        $user->updated_at = date('Y-m-d H:i:s');
                        
                        // dd($user);
                        if ($user->save()) {

                             // Update or create bank information
                                $bankInformation = BankInformation::updateOrCreate(
                                    ['user_id' => $user->id],
                                    [
                                        'bank_name' => $request->bank_name,
                                        'account_number' => $request->account_number,
                                        'account_holder_name' => $request->account_holder_name,
                                        'ifsc_code' => $request->ifsc_code,
                                        'branch_name' => $request->branch_name,
                                        'bank_address' => $request->bank_address,

                                    ]
                                );
                            // DB::table('model_has_roles')->where('model_id',$id)->delete();
                            //$user->assignRole($request->post('roles'));
                            $request->session()->flash('success', 'Client updated successfully');
                            return redirect()->route('admin.users.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.users.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.users.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.users.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.users.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function userdestory(Request $request)
    {
        $id = $request->id;
        $record = User::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.users.index')->with('success', 'Client deleted Successfully.');;
   
    }

    public function changeUserStatus(Request $request)
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


    public function getStatelistByCountryId(Request $request){
    	if($request->selectedValues){
    	try{
    		$data = State::whereIn("country_id",$request->selectedValues)->where("status","active")->get(['id',"name"]);
	     	
            if ($data->count() > 0) {
	     		
	     		$response['status'] = true;
	     		$response['data'] = $data;
	     	} else {
	     		$response['status'] =  true;
	     		$response['data'] = null;
			}
			}catch (\Exception $ex) {
				$response['status'] =  false;
	     		$response['data'] = null;
			}
		}
		else{
				$response['status'] =  false;
     		$response['data'] = null;
	
		}
		return response()->json($response);
    }

    public function getCitylistByStateId(Request $request){
    	if($request->selectedValues){
 		
 		try{
    		$data = City::whereIn("state_id",$request->selectedValues)->where("status","active")->get(['id',"name"]);
	     	if ($data->count() > 0) {
	     		
	     		$response['status'] = true;
	     		$response['data'] = $data;
	     	} else {
	     		$response['status'] =  true;
	     		$response['data'] = null;
			}
			}catch (\Exception $ex) {
				$response['status'] =  false;
	     		$response['data'] = null;
			}
			return response()->json($response);	
    	}
    }


    public function updateDeviceToken(Request $request)
    {
        auth()->user()->update(['device_token'=>$request->token]);
        return response()->json(['token saved successfully.']);
       
    }


    
    public function sendFcmNotification(Request $request){
    $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->first();
    $fcm = $firebaseToken;
    if (!$fcm) {
        return response()->json(['message' => 'User does not have a device token'], 400);
    }

    $title = $request->title;
    $description = $request->body;
    $projectId = "notification-bb27e"; # INSERT COPIED PROJECT ID
    $credentialsFilePath = Storage::path('/json/file.json');
 //   dd($credentialsFilePath);

    $client = new Client();
    $client->setAuthConfig($credentialsFilePath);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $client->refreshTokenWithAssertion();
    $token = $client->getAccessToken();
    $access_token = $token['access_token'];
    $headers = [
        "Authorization: Bearer $access_token",
        'Content-Type: application/json'
    ];

    $data = [
        "message" => [
            "token" => $fcm,
            "notification" => [
                "title" => $title,
                "body" => $description,
            ],
        ]
    ];
    $payload = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return response()->json([
            'message' => 'Curl Error: ' . $err
        ], 500);
    } else {
        // return response()->json([
        //     'message' => 'Notification has been sent',
        //     'response' => json_decode($response, true)
        // ]);
        return redirect()->back();
    }


}

}
