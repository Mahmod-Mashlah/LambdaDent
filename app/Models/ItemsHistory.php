<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemsHistory extends Model
{
    use HasFactory;

    protected $fillable = [

        'item_id',

        'updated_type',
        'updated_quantity',
        'updated_unit_price',

        'created_at',
        'upated_at'
    ];

    protected $with = [
        'item',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
