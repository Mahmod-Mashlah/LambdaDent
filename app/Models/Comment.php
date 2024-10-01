<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;


    protected $fillable = [

        'user_id',
        'case_id',
        'comment',

        'created_at',
        'updated_at',
    ];

    protected $with = [
        'user',
        // 'state',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
    public function state()
    {
        return $this->belongsTo(State::class, "case_id");
    }
}
