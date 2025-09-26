<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','base_transaction_id','frequency','cron_expr','next_run_at','occurrences_left'
    ];

    protected $casts = [
        'next_run_at' => 'datetime',
    ];

    public function baseTransaction()
    {
        return $this->belongsTo(Transaction::class, 'base_transaction_id');
    }
}

