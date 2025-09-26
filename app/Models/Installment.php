<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','transaction_id','number','total_installments','installment_amount','due_date','paid_at'
    ];

    protected $casts = [
        'installment_amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}

