<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','name','type','is_joint','currency','initial_balance','created_by'
    ];

    protected $casts = [
        'is_joint' => 'boolean',
        'initial_balance' => 'decimal:2',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'account_members');
    }

    public function creator()
    {
        return $this->belongsTo(Member::class, 'created_by');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

