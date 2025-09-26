<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','tenant_id','role','invited_at','joined_at','is_active'
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'account_members');
    }
}

