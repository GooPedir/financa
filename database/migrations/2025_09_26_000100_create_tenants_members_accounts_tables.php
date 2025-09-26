<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plan')->default('free');
            $table->timestamps();
        });

        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('role', ['OWNER','ADMIN','MEMBER'])->default('MEMBER');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id','tenant_id']);
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['CHECKING','SAVINGS','WALLET','INVESTMENT'])->default('CHECKING');
            $table->boolean('is_joint')->default(false);
            $table->string('currency', 3)->default('BRL');
            $table->decimal('initial_balance', 14, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('account_members', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->primary(['account_id','member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_members');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('members');
        Schema::dropIfExists('tenants');
    }
};

