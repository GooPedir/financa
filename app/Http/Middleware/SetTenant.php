<?php

namespace App\Http\Middleware;

use App\Models\Member;
use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;

class SetTenant
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $tenant = null;

        if ($user) {
            $tenantId = (int)($request->header('X-Tenant-ID') ?? 0);
            if ($tenantId) {
                $tenant = Tenant::query()
                    ->whereKey($tenantId)
                    ->whereExists(function ($q) use ($user) {
                        $q->selectRaw('1')
                          ->from('members')
                          ->whereColumn('members.tenant_id', 'tenants.id')
                          ->where('members.user_id', $user->id)
                          ->where('members.is_active', true);
                    })->first();
            }

            if (!$tenant) {
                $membership = Member::query()
                    ->where('user_id', $user->id)
                    ->where('is_active', true)
                    ->orderByRaw("case when role = 'OWNER' then 0 when role = 'ADMIN' then 1 else 2 end")
                    ->first();
                if ($membership) {
                    $tenant = $membership->tenant;
                }
            }
        }

        TenantContext::set($tenant);

        return $next($request);
    }
}

