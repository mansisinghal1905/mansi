<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class HostPaymentHistory extends Authenticatable
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

    public function fetchHostPaymentHistroy($request, $columns1) {
      
        $query = HostPaymentHistory::where("host_payment_id",$request->host_payment_id)->orderBy('id', 'desc');

        if (isset($request->from_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") >= "' . date("Y-m-d", strtotime($request->from_date)) . '"');
        }
        if (isset($request->end_date)) {
            $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m-%d") <= "' . date("Y-m-d", strtotime($request->end_date)) . '"');
        }

        if (isset($request['search']['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('host_customer_id', 'like', '%' . $request['search']['value'] . '%');
                $q->where('host_payment_id', 'like', '%' . $request['search']['value'] . '%');
            });
        }
        // if (isset($request->status)) {
        //     $query->where('status', $request->status);
        // }

        if (isset($request->order_column)) {
            $categories = $query->orderBy($columns1[$request->order_column], $request->order_dir);
        } else {
            $categories = $query->orderBy('created_at', 'desc');
        }
        return $categories;
    }

    public function getHostCustomer() {
        return $this->belongsTo(HostingCustomer::class, 'host_customer_id', 'id'); 
    }
    
    public function gethostpayment() {
        return $this->belongsTo(HostPayment::class,'host_payment_id','id'); 
    }
}
