<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class TicketDocument extends Authenticatable
{
    use HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        
    ];
    protected $guarded = [];

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


    public function getAttachmentAttribute($details)
    {
        if ($details != '') {
            return asset('public/ticketfile').'/'.$details;
        }
        return asset('images/no_avatar.jpg');
    } 
    public function getUser() {
        return $this->belongsTo(User::class, 'user_id')->where('type',2)->where('id', '!=', 31); 
    }
    

    


    
}
