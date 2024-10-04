<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillCase extends Model
{
    use HasFactory;

    protected $fillable = [

        'bill_id',
        'case_id',
        'case_cost',

        'created_at',
        'updated_at',
    ];
    protected $with = [
        'case',
        // 'comments',

    ];
    public function case()
    {
        return $this->hasMany(State::class, "id");
    }
}
