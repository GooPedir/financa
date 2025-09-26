<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('limit_amount', 14, 2);
            $table->unsignedTinyInteger('closing_day');
            $table->unsignedTinyInteger('due_day');
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('card_id')->constrained('cards')->cascadeOnDelete();
            $table->date('reference_month');
            $table->date('closing_date');
            $table->date('due_date');
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->enum('status', ['OPEN','CLOSED','PAID'])->default('OPEN');
            $table->timestamps();
            $table->unique(['card_id','reference_month']);
        });

        Schema::create('card_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('card_id')->constrained('cards')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('original_transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->unsignedInteger('installments_total')->default(1);
            $table->unsignedInteger('installment_number')->default(1);
            $table->decimal('per_installment_amount', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('card_purchases');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('cards');
    }
};

