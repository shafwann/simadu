<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceResetRequest extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'reason',
        'approved_at',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
