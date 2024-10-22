<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class Task extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
       
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function fetchTask($request, $columns) {
      
        // $query = Task::distinct('project_id');
        $query = Task::select('id','project_id', 'developer_id')
             ->distinct();
                 
        if(Auth::user()->role == 2){
            $query->where('developer_id',Auth::user()->id);
            // dd($query);
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
                $q->where('description', 'like', '%' . $searchValue . '%')
                ->orWhere('project_id', 'like', '%' . $searchValue . '%');

            });
        }


        if (isset($request->order_column)) {
            $categories = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $categories = $query->orderBy('created_at', 'desc');
        }
       
        return $categories;
    }

    public function fetchshowTask($request, $columns1) {
        // dd($request->all());
        // $developerId = request()->input('developer_id');
        $query = Task::orderBy('id', 'desc');
                // ->where('developer_id', $developerId)

                // Filter by developer_id if provided
        if (isset($request->developer_id) && !empty($request->developer_id)) {
            $query->where('developer_id', $request->developer_id);
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
                $q->where('description', 'like', '%' . $searchValue . '%')
                ->orWhere('project_id', 'like', '%' . $searchValue . '%');

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

    public function getClientuser() {
        return $this->belongsTo(ClientUser::class, 'client_id', 'id')->where('status','!=','0'); 
    }
    public function getProject() {
        return $this->belongsTo(Project::class, 'project_id', 'id')->where('status','!=','0'); 
    }

    public function getDeveloper() {
        return $this->belongsTo(User::class, 'developer_id', 'id')->where('status','!=','0')->where('role', 2)->where('id', '!=', 1); 
    }
}
