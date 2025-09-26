<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model) {
            if (!$model->getAttribute('tenant_id')) {
                $model->setAttribute('tenant_id', TenantContext::id());
            }
        });
    }
}

