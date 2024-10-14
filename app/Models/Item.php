<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [

        'subcategory_id',

        'name',
        'quantity',
        'unit_price',

        'created_at',
        'upated_at'
    ];

    protected $with = [
        'subcategory',
    ];
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function itemHistory()
    {
        return $this->hasMany(ItemsHistory::class);
    }
}
