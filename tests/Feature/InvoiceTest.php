<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Card;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    public function test_close_and_pay_invoice(): void
    {
        $user = User::create(['name'=>'u','email'=>'u@e.com','password'=>Hash::make('pass')]);
        $tenant = Tenant::create(['name'=>'t','plan'=>'free']);
        $member = Member::create(['user_id'=>$user->id,'tenant_id'=>$tenant->id,'role'=>'OWNER','is_active'=>true]);
        TenantContext::set($tenant);

        $account = Account::create(['tenant_id'=>$tenant->id,'name'=>'acc','type'=>'CHECKING','is_joint'=>false,'currency'=>'BRL','initial_balance'=>0,'created_by'=>$member->id]);
        $account->members()->attach($member->id);
        $card = Card::create(['tenant_id'=>$tenant->id,'account_id'=>$account->id,'name'=>'c','brand'=>'VISA','limit_amount'=>1000,'closing_day'=>Carbon::now()->day,'due_day'=>min(28, Carbon::now()->day+5)]);

        $token = $user->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/cards/'.$card->id.'/purchase', [
                'description' => 'Compra',
                'amount' => 120,
                'date' => Carbon::now()->toDateString(),
                'installments' => 3,
            ])->assertCreated();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/cards/'.$card->id.'/close')
            ->assertOk()
            ->assertJsonStructure(['id','total_amount']);

        $invList = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/cards/'.$card->id.'/invoices')
            ->assertOk()->json();

        $invoiceId = $invList[0]['id'];

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/invoices/'.$invoiceId.'/pay', ['amount'=>40])
            ->assertOk();

        $this->assertDatabaseHas('invoices', ['id'=>$invoiceId]);
    }
}

