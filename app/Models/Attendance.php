<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [];

    public function fetchAttendance($request, $columns)
    {
        // Select the necessary columns
        $query = Attendance::select('employee_id', DB::raw('MAX(created_at) as created_at'))
                                    ->where('employee_id', '!=', 1)
                                    ->whereNull('deleted_at')
                                    ->groupBy('employee_id');

        // $query = Attendance::select('employee_id',DB::raw('MAX(created_at) as created_at'))
        // ->where('employee_id', '!=', 1)
        // ->whereNull('deleted_at')
        // ->groupBy('employee_id', DB::raw('DATE(created_at)'))->get(); // Group by employee and attendance date
                
      
        // If the user is an employee (role 2), filter by their ID
        if (Auth::user()->role == 2) {
            $query->where('employee_id', Auth::id());
        }

        // Filter by date range if provided
        if (isset($request->from_date)) {
            $query->whereDate('created_at', '>=', date("Y-m-d", strtotime($request->from_date)));
        }
        if (isset($request->end_date)) {
            $query->whereDate('created_at', '<=', date("Y-m-d", strtotime($request->end_date)));
        }

        // Handle search functionality
        if (isset($request['search']['value']) && !empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];

            // Add search logic for specific columns, adjust as per your need
            $query->where(function ($q) use ($searchValue) {
                $q->where('employee_id', 'like', "%$searchValue%")
                ->orWhere('created_at', 'like', "%$searchValue%");
            });
        }

        // Handle ordering
        if (isset($request->order_column)) {
            $categories = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $categories = $query->orderBy('created_at', 'desc');
        }

        // Return the query results
        return $categories;
    }

    public function fetchshowattendance($request, $columns1) {
        // dd($request->all());
        // $developerId = request()->input('developer_id');
        $query = Attendance::where('employee_login_time', null)->whereNull('deleted_at')->orderBy('id', 'desc');
                // ->where('developer_id', $developerId)

                // Filter by developer_id if provided
        if (isset($request->employee_id) && !empty($request->employee_id)) {
            $query->where('employee_id', $request->employee_id);
        }
        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        
        if (isset($request['search']['value']) && !empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];

            $query->where(function ($q) use ($searchValue) {
                // $q->where('description', 'like', '%' . $searchValue . '%')
                // ->orWhere('project_id', 'like', '%' . $searchValue . '%');

            });
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->order_column)) {
            $categories = $query->orderBy($columns1[$request->order_column], $request->order_dir);
        } else {
            $categories = $query->orderBy('created_at', 'desc');
        }
        // dd($categories->tosql());
        return $categories;
    }

    public function user()
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }
}
