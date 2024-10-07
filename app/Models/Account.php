<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [

        'client_id',
        'bill_id',

        'type',
        'signed_value',
        'current_account',

        'created_at',
        'updated_at',
    ];

    protected $with = [
        // 'client',
        "bill",
    ];
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
    public function client()
    {
        return $this->belongsTo(User::class, "client_id");
    }
}
