<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPolicyTest extends TestCase
{
    public function test_only_account_members_can_view(): void
    {
        $owner = User::create(['name'=>'o','email'=>'o@e.com','password'=>Hash::make('pass')]);
        $other = User::create(['name'=>'x','email'=>'x@e.com','password'=>Hash::make('pass')]);

        $tenant = Tenant::create(['name'=>'t','plan'=>'free']);
        $mOwner = Member::create(['user_id'=>$owner->id,'tenant_id'=>$tenant->id,'role'=>'OWNER','is_active'=>true]);
        $mOther = Member::create(['user_id'=>$other->id,'tenant_id'=>$tenant->id,'role'=>'MEMBER','is_active'=>true]);
        TenantContext::set($tenant);
        $account = Account::create(['tenant_id'=>$tenant->id,'name'=>'acc','type'=>'CHECKING','is_joint'=>false,'currency'=>'BRL','initial_balance'=>0,'created_by'=>$mOwner->id]);
        $account->members()->attach($mOwner->id);

        $this->assertTrue($owner->can('view', $account));
        $this->assertFalse($other->can('view', $account));

        $account->members()->attach($mOther->id);
        $this->assertTrue($other->can('view', $account));
    }
}

