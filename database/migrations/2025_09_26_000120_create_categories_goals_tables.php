<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['INCOME','EXPENSE']);
            $table->string('color', 7)->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('period', ['MONTHLY','YEARLY','CUSTOM'])->default('MONTHLY');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('target_amount', 14, 2);
            $table->decimal('current_amount', 14, 2)->default(0);
            $table->enum('status', ['ACTIVE','ACHIEVED','EXPIRED'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
        Schema::dropIfExists('categories');
    }
};

