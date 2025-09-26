<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Member;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with('members.user')->get();
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:CHECKING,SAVINGS,WALLET,INVESTMENT',
            'currency' => 'nullable|string|size:3',
            'initial_balance' => 'nullable|numeric',
            'is_joint' => 'boolean',
        ]);

        $member = Member::where('user_id', $request->user()->id)
            ->where('tenant_id', TenantContext::id())
            ->firstOrFail();

        $account = Account::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'currency' => $data['currency'] ?? 'BRL',
            'initial_balance' => $data['initial_balance'] ?? 0,
            'is_joint' => (bool)($data['is_joint'] ?? false),
            'created_by' => $member->id,
            'tenant_id' => TenantContext::id(),
        ]);

        $account->members()->attach($member->id);

        return response()->json($account->load('members.user'), 201);
    }

    public function show(Request $request, $id)
    {
        $account = Account::with('members.user')->findOrFail($id);
        Gate::authorize('view', $account);
        return response()->json($account);
    }

    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);
        Gate::authorize('update', $account);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'currency' => 'sometimes|string|size:3',
            'is_joint' => 'sometimes|boolean',
        ]);

        $account->fill($data)->save();

        return response()->json($account);
    }

    public function addMember(Request $request, $id)
    {
        $account = Account::with('members')->findOrFail($id);
        Gate::authorize('manageMembers', $account);

        $data = $request->validate([
            'member_id' => 'required|exists:members,id',
        ]);

        $account->members()->syncWithoutDetaching([$data['member_id']]);
        $account->is_joint = true;
        $account->save();

        return response()->json($account->load('members.user'));
    }
}

