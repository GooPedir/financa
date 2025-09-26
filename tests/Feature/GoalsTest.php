<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GoalsTest extends TestCase
{
    public function test_goal_progress_endpoint(): void
    {
        $user = User::create(['name'=>'u','email'=>'g@e.com','password'=>Hash::make('pass')]);
        $tenant = Tenant::create(['name'=>'t','plan'=>'free']);
        $member = Member::create(['user_id'=>$user->id,'tenant_id'=>$tenant->id,'role'=>'OWNER','is_active'=>true]);
        TenantContext::set($tenant);
        $account = Account::create(['tenant_id'=>$tenant->id,'name'=>'acc','type'=>'CHECKING','is_joint'=>false,'currency'=>'BRL','initial_balance'=>0,'created_by'=>$member->id]);
        $account->members()->attach($member->id);
        $cat = Category::create(['tenant_id'=>$tenant->id,'name'=>'MetaCat','type'=>'EXPENSE','is_active'=>true]);
        $goal = Goal::create(['tenant_id'=>$tenant->id,'category_id'=>$cat->id,'period'=>'MONTHLY','target_amount'=>100,'current_amount'=>10]);
        Transaction::create(['tenant_id'=>$tenant->id,'account_id'=>$account->id,'member_id'=>$member->id,'type'=>'EXPENSE','category_id'=>$cat->id,'description'=>'Gasto','amount'=>25,'date'=>now()->toDateString()]);

        $token = $user->createToken('api')->plainTextToken;
        $res = $this->withHeader('Authorization','Bearer '.$token)->getJson('/api/goals/'.$goal->id.'/progress')
            ->assertOk()->json();
        $this->assertArrayHasKey('percent', $res);
    }
}

