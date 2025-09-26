<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Card;
use App\Models\Category;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $tenant = Tenant::create(['name' => 'Demo Tenant', 'plan' => 'free']);
        $member = Member::create([
            'user_id' => $user->id,
            'tenant_id' => $tenant->id,
            'role' => 'OWNER',
            'invited_at' => now(),
            'joined_at' => now(),
            'is_active' => true,
        ]);

        $account = Account::create([
            'tenant_id' => $tenant->id,
            'name' => 'Conta Corrente',
            'type' => 'CHECKING',
            'is_joint' => false,
            'currency' => 'BRL',
            'initial_balance' => 1000,
            'created_by' => $member->id,
        ]);
        $account->members()->attach($member->id);

        $card = Card::create([
            'tenant_id' => $tenant->id,
            'account_id' => $account->id,
            'name' => 'Visa Demo',
            'brand' => 'VISA',
            'limit_amount' => 5000,
            'closing_day' => 10,
            'due_day' => 20,
        ]);

        Category::insert([
            ['tenant_id' => $tenant->id, 'name' => 'Salário', 'type' => 'INCOME', 'color' => '#086A54', 'is_active' => true],
            ['tenant_id' => $tenant->id, 'name' => 'Alimentação', 'type' => 'EXPENSE', 'color' => '#ff6b6b', 'is_active' => true],
        ]);

        Transaction::create([
            'tenant_id' => $tenant->id,
            'account_id' => $account->id,
            'member_id' => $member->id,
            'type' => 'INCOME',
            'description' => 'Depósito inicial',
            'amount' => 1000,
            'date' => now()->toDateString(),
        ]);
    }
}
