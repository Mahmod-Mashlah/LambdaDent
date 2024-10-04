<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [

        'client_id',

        'patient_name',
        'age',
        'gender',
        'need_trial',
        'repeat',
        'shade',
        'expected_delivery_date',
        'notes',
        'status',
        'confirm_delivery',
        'cost',

        'teeth_crown',
        'teeth_pontic',
        'teeth_implant',
        'teeth_veneer',
        'teeth_inlay',
        'teeth_denture',

        'bridges_crown',
        'bridges_pontic',
        'bridges_implant',
        'bridges_veneer',
        'bridges_inlay',
        'bridges_denture',

        'created_at',
        'updated_at',
    ];

    protected $with = [
        'client',
        // 'comments',
        'images',
    ];
    public function images()
    {
        return $this->hasMany(File::class, "case_id");
    }
    public function client()
    {
        return $this->belongsTo(User::class, "client_id");
    }
    public function comments()
    {
        return $this->hasMany(Comment::class, "case_id");
    }
    public function bill()
    {
        return $this->belongsTo(BillCase::class);
    }
}
