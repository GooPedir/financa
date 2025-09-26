<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id','account_id','member_id','type','category_id','description','amount','date','tags','notes','transfer_group_id'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'tags' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function splits()
    {
        return $this->hasMany(TransactionSplit::class);
    }
}

