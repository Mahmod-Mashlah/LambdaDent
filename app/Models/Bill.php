<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $fillable = [

        'client_id',

        'code_number',
        'total_cost',
        'date_from',
        'date_to',

        'created_at',
        'updated_at',

    ];
    protected $with = [
        'client',
        // 'comments',
        'bill_cases',
    ];
    public function bill_cases()
    {
        return $this->hasMany(BillCase::class, "id");
    }
    public function client()
    {
        return $this->belongsTo(User::class, "client_id");
    }
}
