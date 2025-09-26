<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Member;
use App\Models\User;

class AccountPolicy
{
    public function view(User $user, Account $account): bool
    {
        return $this->isMemberOfAccount($user, $account);
    }

    public function update(User $user, Account $account): bool
    {
        return $this->isMemberOfAccount($user, $account);
    }

    public function manageMembers(User $user, Account $account): bool
    {
        if (!$this->isMemberOfAccount($user, $account)) {
            return false;
        }
        $member = Member::where('user_id', $user->id)
            ->where('tenant_id', $account->tenant_id)
            ->first();
        return in_array($member?->role, ['OWNER','ADMIN'], true);
    }

    protected function isMemberOfAccount(User $user, Account $account): bool
    {
        return $account->members()
            ->where('members.user_id', $user->id)
            ->exists();
    }
}

