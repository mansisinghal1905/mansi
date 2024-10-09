<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class QuotationMoreDetail extends Authenticatable
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


    public function getCategory() {
        return $this->belongsTo(Category::class, 'category_id', 'id')->where('id', '!=', 1)->where('status','!=','0'); 
    }

    public function getQotation() {
        return $this->belongsTo(QuotationDetail::class, 'quotation_details_id', 'id')->where('status','!=','0'); 
    }
    
}
