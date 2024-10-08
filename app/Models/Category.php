<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [

        'name',

        'created_at',
        'updated_at',

    ];

    // public function bill_cases()
    // {
    //     return $this->hasMany(BillCase::class, "id");
    // }

}
