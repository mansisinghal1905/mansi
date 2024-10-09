<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class QuotationSendMail extends Authenticatable
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

    public function fetchmailQuotation($request, $columns) {
      
        $query = QuotationSendMail::orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        // if (isset($request['search']['value'])) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('title', 'like', '%' . $request['search']['value'] . '%');
        //         $q->where('project_id', 'like', '%' . $request['search']['value'] . '%');

        //     });
        // }
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

    public function getClient() {
        return $this->belongsTo(User::class, 'client_id', 'id')->where('id', '!=', 1)->where('status','!=','0'); 
    }
    public function getQuotation() {
        return $this->belongsTo(QuotationDetail::class, 'quotation_id', 'id')->where('status','!=','0'); 
    }
   
}
