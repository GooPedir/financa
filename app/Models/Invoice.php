<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','card_id','reference_month','closing_date','due_date','total_amount','paid_amount','status'
    ];

    protected $casts = [
        'reference_month' => 'date',
        'closing_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}

