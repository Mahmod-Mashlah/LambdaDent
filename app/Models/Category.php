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

    protected $with = [
        // "subcategories"
    ];
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
