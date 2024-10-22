<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Mail\UserDetailsMail;
use Illuminate\Support\Str;
use DateTime;
use DB;
use Mail;
use Hash;
use Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    function __construct()
    {
        $this->Model = new Attendance;
        $this->columns = [
            "id",
            "employee_id",
            "remending_hours",
            "total_hours",
            "status",
            "created_at",

        ];
        $this->columns1 = [
            "id",
            "checkout_from_break",
            "checkin_from_break",
            "remending_hours",
            "message",
            "created_at",

        ];
    }

    public function index()
    {
        $attendance = Attendance::all();
        return view('admin.attendances.index',compact('attendance'));
    }

    // public function attendanceAjax(Request $request)
    // {
    //     $request->search = $request->search;
    //     if (isset($request->order[0]['column'])) {
    //         $request->order_column = $request->order[0]['column'];
    //         $request->order_dir = $request->order[0]['dir'];
    //     }
    //     $records = $this->Model->fetchAttendance($request, $this->columns);
    //     // dd($records->get());
    //     $total = $records->get();
    //     if (isset($request->start)) {
    //         $categories = $records->offset($request->start)->limit($request->length)->get();
    //     } else {
    //         $categories = $records->offset($request->start)->limit(count($total))->get();
    //     }
    //     $result = [];
    //     $i = $request->start;
    //     foreach ($categories as $value) {
    //         $getoffilealhrs = Attendance::select('office_logout_hrs','employee_login_time','employee_id','created_at')->where("employee_id",$value->employee_id)->whereNull('deleted_at')->first();
            
    //         $getlateWorkingHrs = Attendance::wherenotNull('remending_hours')->where("employee_id",$getoffilealhrs->employee_id)->pluck('remending_hours')->toArray();
    //         // dd($getlateWorkingHrs);
    //         $lettime = "0:00";
    //         $emptotalworkinghrs = "0:00";
    //         if(count($getlateWorkingHrs) > 0)
    //         {
    //             $timeArray = $getlateWorkingHrs;
    //             $totalMinutes = 0;
    //             // Convert the time values to total minutes
    //             foreach ($timeArray as $time) {
    //                 list($hours, $minutes) = explode(':', $time);
    //                 $totalMinutes += ($hours * 60) + $minutes; // Convert hours to minutes and add to total
    //             }
    //             // Convert total time to hours and minutes
    //             $totalHours = floor($totalMinutes / 60); // Convert back to hours
    //             $remainingMinutes = $totalMinutes % 60;  // Get remaining minutes
    //             $lettime =$totalHours.":".$remainingMinutes;
                
    //             // Subtract the total time from 9:30 hours (570 minutes)
    //             $limitMinutes = (9 * 60) + 30; // 9 hours 30 minutes = 570 minutes
    //             $differenceMinutes = $limitMinutes - $totalMinutes;
                
    //             // Check if the total time exceeds the limit
    //             if ($differenceMinutes > 0) {
    //                 $remainingHours = floor($differenceMinutes / 60);
    //                 $remainingMinutes = $differenceMinutes % 60;
    //                 $emptotalworkinghrs = $remainingHours.':'. $remainingMinutes;
    //             } 
    //         }

    //         $data = [];
    //         $data['id'] = ++$i;
    //         $data['created_at'] = date('Y-m-d', strtotime($getoffilealhrs->created_at));;

    //         $data['employee_login_time'] = $getoffilealhrs->employee_login_time;
    //         $data['remending_hours'] = $lettime;
    //         $data['total_hours'] = $emptotalworkinghrs;
         
    //          $data['status'] = $lettime != 0:00 
    //          ? '<span class="badge bg-danger nxl-h-badge" >Incomplete</span>' 
    //          : '<span class="badge bg-success nxl-h-badge">Complete</span>';
         
    //         $data['employee_id'] = $value->user->name;

            
    //         $action = '<div class="actionBtn d-flex align-itemss-center" style="gap:8px">';

    //         $url = route('admin.attendancesshow', [
    //             'employee_id' => $value['employee_id']
    //         ]);
    //         // $action .= '<a href="' . route('admin.attendances.edit', $value->id) . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="Edit"><i class="fa fa-pencil"></i></a>';
            
    //         $action .= '<a href="' . $url . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';

    //         // $action .= '<a href="javascript:void(0)" onclick="deleteDevelopers(this)" data-url="' . route('admin.developerdestory') . '" class="toolTip deleteDevelopers" data-toggle="tooltip" data-id="' . $value->id . '" data-placement="bottom" title="Delete"><i class="fa fa-times"></i></a>';
    //         $action.="</div>";

    //         $data['view'] = $action;
    //         // $data['status'] = $status;
    //         $result[] = $data;

    //     }
    //     $data = json_encode([
    //         'data' => $result,
    //         'recordsTotal' => count($total),
    //         'recordsFiltered' => count($total),
    //     ]);
    //     return $data;
    // }
    public function attendanceAjax(Request $request) {
    $request->search = $request->search;
    
    if (isset($request->order[0]['column'])) {
        $request->order_column = $request->order[0]['column'];
        $request->order_dir = $request->order[0]['dir'];
    }
    
    $records = $this->Model->fetchAttendance($request, $this->columns);
    $total = $records->get();

    if (isset($request->start)) {
        $categories = $records->offset($request->start)->limit($request->length)->get();
    } else {
        $categories = $records->offset($request->start)->limit(count($total))->get();
    }

    $result = [];
    $i = $request->start;

    foreach ($categories as $value) {
        $getoffilealhrs = Attendance::select('office_logout_hrs','employee_login_time','employee_id','created_at')
            ->where("employee_id", $value->employee_id)
            ->whereNull('deleted_at')
            ->first();
        
        // Retrieve remending_hours for late working hours
        $getlateWorkingHrs = Attendance::whereNotNull('remending_hours')
            ->where("employee_id", $getoffilealhrs->employee_id)
            ->pluck('remending_hours')
            ->toArray();

       


        
        $lettime = "0:00";
        // $emptotalworkinghrs = "0:00";

        // Check if there are late working hours (break hours)
        if (count($getlateWorkingHrs) > 0) {
            $timeArray = $getlateWorkingHrs;
            $totalMinutes = 0;

            foreach ($timeArray as $time) {
                list($hours, $minutes) = explode(':', $time);
                $totalMinutes += ($hours * 60) + $minutes;
            }

            $totalHours = floor($totalMinutes / 60);
            $remainingMinutes = $totalMinutes % 60;
            $lettime = "{$totalHours}:{$remainingMinutes}";

            $limitMinutes = (9 * 60) + 30; // 9 hours 30 minutes
            $differenceMinutes = $limitMinutes - $totalMinutes;

            if ($differenceMinutes > 0) {
                $remainingHours = floor($differenceMinutes / 60);
                $remainingMinutes = $differenceMinutes % 60;
                // $emptotalworkinghrs = "{$remainingHours}:{$remainingMinutes}";
            $emptotalworkinghrs = sprintf('%02d:%02d', $remainingHours, $remainingMinutes);

            }
        } else {
            // Calculate total hours if no break (use login and logout time)
            $loginTime = new DateTime($getoffilealhrs->employee_login_time);
            $logoutTime = new DateTime($getoffilealhrs->office_logout_hrs);
            $workingTime = $loginTime->diff($logoutTime); // Calculate the time difference
// dd($workingTime);
            // Convert the working time to total hours and minutes
            $workingHours = $workingTime->h;
            $workingMinutes = $workingTime->i;
            // $emptotalworkinghrs = "{$workingHours}:{$workingMinutes}"; // Format as "hours:minutes"
            $emptotalworkinghrs = sprintf('%02d:%02d', $workingHours, $workingMinutes);

        }

        // Populate data array for each employee attendance
        $data = [];
        $data['id'] = ++$i;
        $data['created_at'] = date('Y-m-d', strtotime($getoffilealhrs->created_at));
        $data['employee_login_time'] = $getoffilealhrs->employee_login_time;
        $data['remending_hours'] = $lettime;
        
        $data['total_hours'] = $emptotalworkinghrs; // Total hours based on login and logout time

        // Set status based on total working hours or remaining hours
        $data['status'] = ($lettime != "0:00") 
            ? '<span class="badge bg-danger nxl-h-badge">Incomplete</span>' 
            : '<span class="badge bg-success nxl-h-badge">Complete</span>';

        $data['employee_id'] = $value->user->name;

        // Define action buttons for view details
        $action = '<div class="actionBtn d-flex align-items-center" style="gap:8px">';
        $url = route('admin.attendancesshow', ['employee_id' => $value['employee_id']]);
        $action .= '<a href="' . $url . '" class="toolTip" data-toggle="tooltip" data-placement="bottom" title="View Detail"><i class="fa fa-eye"></i></a>';
        $action .= "</div>";

        $data['view'] = $action;
        $result[] = $data;
    }

    return json_encode([
        'data' => $result,
        'recordsTotal' => count($total),
        'recordsFiltered' => count($total),
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $attendance = null;
        return view('admin.attendances.create',compact('attendance'));
    }

    /**
     * Store a newly created resource in storage.
     */
   
    // public function store(Request $request) {
    //     $input = $request->all();
    //     // dd($input);
    //     $validate = Validator($request->all(), [
    //         'checkin_from_break' => 'required',
    //         'checkout_from_break' => 'required',
    //         'message' => 'required',
    //     ]);
    
    //     $attr = [
    //         'checkin_from_break' => 'Login Time',
    //         'checkout_from_break' => 'Logout Time',
    //         'message' => 'Message',
    //     ];
    //     $validate->setAttributeNames($attr);
       
    //     if ($validate->fails()) {
    //         return redirect()->route('admin.attendances.create')->withInput($request->all())->withErrors($validate);
    //     } else {
    //         try {
    //             $final_checkout_time = '7:00 PM'; // End of day checkout time

    //             $getAttendance = Attendance::where("employee_id",Auth::id())->whereDate('created_at', Carbon::today())->first();
    //             $login_time= $getAttendance->employee_login_time;
    //             // dd($request->all());
    //             $attendance = new Attendance;
    //             $attendance->employee_id = Auth::id();
    //             $attendance->checkin_from_break =$request->checkin_from_break;
    //             $attendance->checkout_from_break = $request->checkout_from_break;
    //             $attendance->message = $request->message;

    //             $calData = $this->calculate($login_time,$request->checkin_from_break, $request->checkout_from_break);
    //             // dd($calData); 
    //             $attendance->remending_hours = $calData['rem_time'];
    //             $attendance->status = $calData['rem_time']  ? 'incomplete':'complete';
    //             $attendance->total_hours = $calData['total_working_time'];  
                
                


    //             $attendance->save();

    //             $request->session()->flash('success', 'Attendance added successfully');
    //             return redirect()->route('admin.attendances.index');
    //         } catch (Exception $e) {
    //             // dd($e);
    //             $request->session()->flash('error', 'Something went wrong. Please try again later.');
    //             return redirect()->route('admin.attendances.index');
    //         }
    //     }
    // }
    
    // public function calculate($lgTime, $breakstTime = null, $breakETime = null)
    // {
    //     // Office start time and actual login/logout times
    //     $officeStartTime = new DateTime('09:30');
    //     $logoutTime = new DateTime('19:00'); // Replace with actual logout time
    //     $loginTime = new DateTime($lgTime); // Replace with actual login time

    //     // Initialize data array
    //     $data = [
    //         'status' => 'complete',
    //         'merged_time' => null,
    //         'total_working_time' => null
    //     ];

    //     $lateMinutes = 0; // Initialize late time in minutes

    //     // Check if login time is later than office start time and calculate late login
    //     if ($loginTime > $officeStartTime) {
    //         // Calculate the late time
    //         $lateDuration = $officeStartTime->diff($loginTime);
    //         $lateMinutes = ($lateDuration->h * 60) + $lateDuration->i; // Convert late time to minutes
    //         $data['status'] = "incomplete";
    //     } else {
    //         $data['status'] = "complete";
    //     }

    //     $breakMinutes = 0; // Initialize break time in minutes

    //     // Check if both break start and end times are provided, then calculate break duration
    //     if ($breakstTime && $breakETime) {
    //         $breakStartTime = new DateTime($breakstTime);
    //         $breakEndTime = new DateTime($breakETime);

    //         // Calculate break time duration
    //         $breakDuration = $breakStartTime->diff($breakEndTime);
    //         $breakMinutes = ($breakDuration->h * 60) + $breakDuration->i; // Convert break time to minutes
    //     }

    //     // Merge late time and break time
    //     $mergedMinutes = $lateMinutes + $breakMinutes;

    //     // Convert back to hours and minutes for merged time
    //     $mergedHours = floor($mergedMinutes / 60);
    //     $mergedMinutesRemaining = $mergedMinutes % 60;

    //     // Store merged time in hours and minutes
    //     $data['rem_time'] = "{$mergedHours}:{$mergedMinutesRemaining}";

    //     // Calculate total working time (before subtracting break time)
    //     $workingTime = $loginTime->diff($logoutTime);

    //     // Convert total working time into minutes
    //     $totalWorkingMinutes = ($workingTime->h * 60) + $workingTime->i;

    //     // Subtract break time from total working minutes
    //     $totalWorkingMinutes -= $breakMinutes;

    //     // Convert back to hours and minutes for total working time
    //     $workingHours = floor($totalWorkingMinutes / 60);
    //     $workingMinutes = $totalWorkingMinutes % 60;

    //     // Store total working time
    //     $data['total_working_time'] = "{$workingHours}:{$workingMinutes}";

    //     return $data;
    // }

    public function store(Request $request) {
        $input = $request->all();
        
        // Validation for required fields
        $validate = Validator($request->all(), [
            'checkin_from_break' => 'required',
            'checkout_from_break' => 'required',
            'message' => 'required',
        ]);
    
        $attr = [
            'checkin_from_break' => 'Login Time',
            'checkout_from_break' => 'Logout Time',
            'message' => 'Message',
        ];
        
        // Set attribute names for validation errors
        $validate->setAttributeNames($attr);
       
        if ($validate->fails()) {
            return redirect()->route('admin.attendances.create')
                ->withInput($request->all())
                ->withErrors($validate);
        } else {
            try {
                // Define the final checkout time
                $final_checkout_time = '7:00 PM';
    
                // Get today's attendance for the logged-in user
                $getAttendance = Attendance::where("employee_id", Auth::id())
                    ->whereDate('created_at', Carbon::today())
                    ->first();
                
                // Get the login time from the attendance record
                $login_time = $getAttendance->employee_login_time;
    
                $attendance = new Attendance;
                $attendance->employee_id = Auth::id();
                $attendance->checkin_from_break = $request->checkin_from_break;
                $attendance->checkout_from_break = $request->checkout_from_break;
                $attendance->message = $request->message;
    
                // Call the calculate function
                $calData = $this->calculate($login_time, $request->checkin_from_break, $request->checkout_from_break);
                
                // Update attendance based on calculated data
                $attendance->remending_hours = $calData['rem_time'];
                $attendance->status = $calData['status'];
                $attendance->total_hours = $calData['total_working_time'];         
                $attendance->save();
    
                // Flash success message
                $request->session()->flash('success', 'Attendance added successfully');
                return redirect()->route('admin.attendances.index');
            } catch (Exception $e) {
                // Handle exceptions and flash error message
                $request->session()->flash('error', 'Something went wrong. Please try again later.');
                return redirect()->route('admin.attendances.index');
            }
        }
    }
    
    public function calculate($lgTime, $breakstTime = null, $breakETime = null) {
        // Office start and end times
        $officeStartTime = new DateTime('09:30');
        $logoutTime = new DateTime('19:00');
        $loginTime = new DateTime($lgTime);
    
        // Initialize data array
        $data = [
            'status' => 'complete',
            'merged_time' => null,
            'total_working_time' => null
        ];
    
        $lateMinutes = 0;
    
        // Calculate late login if login time is after office start time
        if ($loginTime > $officeStartTime) {
            $lateDuration = $officeStartTime->diff($loginTime);
            $lateMinutes = ($lateDuration->h * 60) + $lateDuration->i;
            $data['status'] = "incomplete";
        }
    
        $breakMinutes = 0;
    
        // Check if both break start and end times are provided
        if ($breakstTime && $breakETime) {
            $breakStartTime = new DateTime($breakstTime);
            $breakEndTime = new DateTime($breakETime);
    
            // Calculate break duration
            $breakDuration = $breakStartTime->diff($breakEndTime);
            $breakMinutes = ($breakDuration->h * 60) + $breakDuration->i;
        }
    
        // Merge late and break times
        $mergedMinutes = $lateMinutes + $breakMinutes;
    
        // Convert merged time back to hours and minutes
        $mergedHours = floor($mergedMinutes / 60);
        $mergedMinutesRemaining = $mergedMinutes % 60;
    
        // Set the rem_time attribute for merged time
        // $data['rem_time'] = "{$mergedHours}:{$mergedMinutesRemaining}";
        $data['rem_time'] = sprintf('%02d:%02d', $mergedHours, $mergedMinutesRemaining);

    
        // Calculate total working time without break
        $workingTime = $loginTime->diff($logoutTime);
    
        $totalWorkingMinutes = ($workingTime->h * 60) + $workingTime->i;
    
        // Subtract break time from total working minutes
        if ($breakMinutes > 0) {
            $totalWorkingMinutes -= $breakMinutes;
        }
    
        // Convert back to hours and minutes
        $workingHours = floor($totalWorkingMinutes / 60);
        $workingMinutes = $totalWorkingMinutes % 60;
    
        // Store total working time
        // $data['total_working_time'] = "{$workingHours}:{$workingMinutes}";
        $data['total_working_time'] = sprintf('%02d:%02d', $workingHours, $workingMinutes);

        // Adjust the status if there is no late or break time
        if ($lateMinutes == 0 && $breakMinutes == 0) {
            $data['status'] = 'complete';
        }
    
        return $data;
    }

    
    
    /**
     * Display the specified resource.
     */
    public function attendancesshow($employeeid)
    {
        // $attendance=Attendance::find($id);
        $attendance = Attendance::where('employee_id', $employeeid)
        ->get();

        return view('admin.attendances.view',compact('attendance','employeeid'));
    }

    public function ShowattendanceAjax(Request $request)
    {
        // dd($request->all());
        $request->search = $request->search;
        if (isset($request->order[0]['column'])) {
            $request->order_column = $request->order[0]['column'];
            $request->order_dir = $request->order[0]['dir'];
        }
         // Fetch project statuses
        $records = $this->Model->fetchshowattendance($request, $this->columns1);
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
            $data['employee_id'] = $value->user->name;
            $data['checkout_from_break'] = $value->checkout_from_break;
            $data['checkin_from_break'] = $value->checkin_from_break ?? '-';

            $data['remending_hours'] = $value->remending_hours;
            $data['message'] = Str::limit($value->message, 20);

            $data['created_at'] = date('Y-m-d', strtotime($value->created_at));


            // $status = "<div class='form-check form-switch form-switch-sm'><input class='form-check-input c-pointer clientuserStatusToggle' type='checkbox' id='formSwitchDropbox_{$value->id}' data-id='{$value->id}'" . ($value->status == 1 ? 'checked' : '') . "><label class='form-check-label fw-500 text-dark c-pointer' for='formSwitchDropbox_{$value->id}'>" . ($value->status == 1 ? 'Active' : 'Inactive') . "</label></div>";
            // if(Auth::user()->role == 2){
        
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
            $attendance = Attendance::find($id);
            
            if (isset($attendance->id)) {
            
                $type = 'edit';

                return view('admin.attendances.create', compact('attendance', 'type'));
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.attendances.index');
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.attendances.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (isset($id) && $id != null) {

            $attendance = Attendance::where('id', $id)->first();

            if (isset($attendance->id)) {
                $validate = Validator($request->all(),  [
                    'checkin_from_break' => 'required',
                    'checkout_from_break' => 'required',
                    'message' => 'required',
                ]);
                $attr = [
                    'checkin_from_break' => 'Login Time',
                    'checkout_from_break' => 'Logout Time',
                    'message' => 'Message',
                ];

                $validate->setAttributeNames($attr);

                if ($validate->fails()) {
                    return redirect()->route('admin.attendances.edit', ['id' => $attendance->id])->withInput($request->all())->withErrors($validate);
                } else {
                    try {
                      
                        $attendance->checkin_from_break = $request->checkin_from_break;
                        $attendance->checkout_from_break = $request->checkout_from_break;
                        $attendance->message = $request->message;
        
                        if ($attendance->save()) {
                          
                            $request->session()->flash('success', 'Attendance updated successfully');
                            return redirect()->route('admin.attendances.index');
                        } else {
                            $request->session()->flash('error', 'Something went wrong. Please try again later.');
                            return redirect()->route('admin.attendances.edit', ['id' => $id]);
                        }
                    } catch (Exception $e) {
                        $request->session()->flash('error', 'Something went wrong. Please try again later.');
                        return redirect()->route('admin.attendances.edit', ['id' => $id]);
                    }
                }
            } else {
                $request->session()->flash('error', 'Invalid Data');
                return redirect()->route('admin.attendances.edit', ['id' => $id]);
            }
        } else {
            $request->session()->flash('error', 'Invalid Data');
            return redirect()->route('admin.attendances.edit', ['id' => $id]);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function developerdestory(Request $request)
    {
        $id = $request->id;
        $record = Attendance::findOrFail($id); 
        $record->delete();

        return redirect()->route('admin.attendances.index')->with('success', 'Developer deleted Successfully.');;
   
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
