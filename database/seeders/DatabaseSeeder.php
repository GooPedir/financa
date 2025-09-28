<?php

namespace Database\\Seeders;

use App\\Models\\Account;
use App\\Models\\Card;
use App\\Models\\Category;
use App\\Models\\Member;
use App\\Models\\Tenant;
use App\\Models\\Transaction;
use App\\Models\\User;
use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

         = Tenant::create(['name' => 'Demo Tenant', 'plan' => 'free']);
         = Member::create([
            'user_id' => ->id,
            'tenant_id' => ->id,
            'role' => 'OWNER',
            'invited_at' => now(),
            'joined_at' => now(),
            'is_active' => true,
        ]);

         = Account::create([
            'tenant_id' => ->id,
            'name' => 'Conta Corrente',
            'type' => 'CHECKING',
            'is_joint' => false,
            'currency' => 'BRL',
            'initial_balance' => 1000,
            'created_by' => ->id,
        ]);
        ->members()->attach(->id);

         = Card::create([
            'tenant_id' => ->id,
            'account_id' => ->id,
            'name' => 'Visa Demo',
            'brand' => 'VISA',
            'limit_amount' => 5000,
            'closing_day' => 10,
            'due_day' => 20,
        ]);

        Category::insert([
            ['tenant_id' => ->id, 'name' => 'Salario', 'type' => 'INCOME', 'color' => '#086A54', 'is_active' => true],
            ['tenant_id' => ->id, 'name' => 'Alimentacao', 'type' => 'EXPENSE', 'color' => '#ff6b6b', 'is_active' => true],
        ]);

        Transaction::create([
            'tenant_id' => ->id,
            'account_id' => ->id,
            'member_id' => ->id,
            'type' => 'INCOME',
            'description' => 'Deposito inicial',
            'amount' => 1000,
            'date' => now()->toDateString(),
        ]);
    }
}
