<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function me(Request $request)
    {
        $tenant = TenantContext::get();
        $memberships = Member::with('user')
            ->where('tenant_id', $tenant?->id)
            ->get();
        return response()->json([
            'tenant' => $tenant,
            'members' => $memberships,
        ]);
    }
}

