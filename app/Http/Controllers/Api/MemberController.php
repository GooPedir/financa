<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $tenant = TenantContext::get();
        $members = Member::with('user')
            ->where('tenant_id', $tenant?->id)
            ->get();
        return response()->json($members);
    }

    public function invite(Request $request)
    {
        $tenant = TenantContext::get();
        $data = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:OWNER,ADMIN,MEMBER',
        ]);

        $user = User::firstOrCreate(['email' => $data['email']], [
            'name' => $data['email'],
            'password' => bcrypt(str()->random(16)),
        ]);

        $member = Member::updateOrCreate([
            'user_id' => $user->id,
            'tenant_id' => $tenant?->id,
        ], [
            'role' => $data['role'],
            'invited_at' => now(),
            'is_active' => true,
        ]);

        return response()->json($member, 201);
    }
}

