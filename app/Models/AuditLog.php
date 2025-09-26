<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use BelongsToTenant;

    public $timestamps = false;

    protected $fillable = [
        'tenant_id','actor_member_id','entity','entity_id','action','changes','created_at'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];
}

