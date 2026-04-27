<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prompt',
        'result',
        'type',
        'model_used',
        'tokens_used',
        'cost_usd',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'cost_usd' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
