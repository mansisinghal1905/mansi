<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    use HasFactory;
    protected $table = "notifications";

    public function getuser() {
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }
}
