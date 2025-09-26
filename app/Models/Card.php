<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','account_id','name','brand','limit_amount','closing_day','due_day'
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

