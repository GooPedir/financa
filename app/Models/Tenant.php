<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = ['name','plan'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}

