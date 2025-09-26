<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardPurchase extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','card_id','invoice_id','original_transaction_id','installments_total','installment_number','per_installment_amount'
    ];

    protected $casts = [
        'per_installment_amount' => 'decimal:2',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

