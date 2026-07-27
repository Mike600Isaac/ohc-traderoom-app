<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_entitlement_id')->nullable()->constrained()->nullOnDelete();
            $table->string('course_id');
            $table->string('course_name');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('USD');
            $table->string('reference')->unique();
            $table->string('access_code')->nullable();
            $table->string('authorization_url')->nullable();
            $table->string('status')->default('pending');
            $table->string('gateway_response')->nullable();
            $table->string('paystack_transaction_id')->nullable();
            $table->json('metadata')->nullable();
            $table->json('verified_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
