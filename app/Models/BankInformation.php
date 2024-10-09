<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInformation extends Model
{
    use HasFactory;

    // Specify the table associated with the model
    protected $table = 'bank_informations';

    // Specify the primary key if it's not the default 'id'
    protected $primaryKey = 'id';

    // Disable timestamps if you don't want created_at and updated_at columns
    public $timestamps = true;

    // Define the fillable properties (mass assignment protection)
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
        'account_holder_name',
        'ifsc_code',
        'branch_name',
        'bank_address'
    ];

    // Define any relationships, for example, with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
