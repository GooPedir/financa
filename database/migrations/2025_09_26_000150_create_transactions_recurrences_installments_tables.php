<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('type', ['INCOME','EXPENSE','TRANSFER','CARD_PURCHASE','CARD_PAYMENT']);
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 14, 2);
            $table->date('date');
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->string('transfer_group_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('transaction_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('recurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('base_transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->enum('frequency', ['DAILY','WEEKLY','MONTHLY','YEARLY','CRON']);
            $table->string('cron_expr')->nullable();
            $table->timestamp('next_run_at');
            $table->integer('occurrences_left')->nullable();
            $table->timestamps();
        });

        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->unsignedInteger('total_installments');
            $table->decimal('installment_amount', 14, 2);
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
        Schema::dropIfExists('recurrences');
        Schema::dropIfExists('transaction_splits');
        Schema::dropIfExists('transactions');
    }
};

