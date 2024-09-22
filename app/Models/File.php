<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        "case_id",
        "is_case_image",
        "name",
    ];
    public function case_details()
    {
        return $this->belongsTo(State::class, "case_id");
    }
}
