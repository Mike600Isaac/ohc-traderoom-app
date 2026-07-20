<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_entitlements', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('offer_name');
            $table->string('offer_type')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_entitlements', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'offer_type']);
        });
    }
};
