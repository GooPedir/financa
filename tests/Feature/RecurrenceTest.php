<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Member;
use App\Models\Recurrence;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecurrenceTest extends TestCase
{
    public function test_generate_recurrence_creates_transaction(): void
    {
        $user = User::create(['name'=>'u','email'=>'r@e.com','password'=>Hash::make('pass')]);
        $tenant = Tenant::create(['name'=>'t','plan'=>'free']);
        $member = Member::create(['user_id'=>$user->id,'tenant_id'=>$tenant->id,'role'=>'OWNER','is_active'=>true]);
        TenantContext::set($tenant);
        $account = Account::create(['tenant_id'=>$tenant->id,'name'=>'acc','type'=>'CHECKING','is_joint'=>false,'currency'=>'BRL','initial_balance'=>0,'created_by'=>$member->id]);
        $account->members()->attach($member->id);
        $base = Transaction::create(['tenant_id'=>$tenant->id,'account_id'=>$account->id,'member_id'=>$member->id,'type'=>'EXPENSE','description'=>'Recorrente','amount'=>10,'date'=>now()->toDateString()]);
        Recurrence::create(['tenant_id'=>$tenant->id,'base_transaction_id'=>$base->id,'frequency'=>'DAILY','next_run_at'=>now()->subMinute()]);

        dispatch_sync(new \App\Jobs\ProcessRecurrences);

        $this->assertDatabaseHas('transactions', ['description'=>'Recorrente','amount'=>10]);
    }
}

