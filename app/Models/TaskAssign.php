<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Auth;

class TaskAssign extends Authenticatable
{
    use HasFactory, Notifiable;

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
      
        $query = TaskAssign::where('status','!=',2)
                            ->orderBy('id', 'desc');

        // Check if the user is an admin
        if (Auth::user()->role == 2) {
            // If not an admin, filter by the logged-in user's tasks
            $query->where('developer_id', Auth::id());
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
                $q->where('task_title', 'like', '%' . $searchValue . '%');
               

            });
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->order_column)) {
            $categories = $query->orderBy($columns[$request->order_column], $request->order_dir);
        } else {
            $categories = $query->orderBy('created_at', 'desc');
        }
        return $categories;
    }

    public function getProject() {
        return $this->belongsTo(Project::class, 'project_id', 'id')->where('status','!=','0'); 
    }

    public function getDeveloper() {
        return $this->belongsTo(User::class, 'developer_id', 'id')->where('status','!=','0')->where('role', 2)->where('id', '!=', 1); 
    }

    public function getTaskassign() {
        return $this->belongsTo(ProjectAssign::class, 'project_id', 'id')->where('status','!=','0'); 
    }
}
