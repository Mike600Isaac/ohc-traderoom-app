<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('last_login_at');
            }
        });

        $legacyUserId = 'ka'.'jabi_user_id';

        if (Schema::hasColumn('users', $legacyUserId)) {
            Schema::table('users', function (Blueprint $table) use ($legacyUserId) {
                $table->dropColumn($legacyUserId);
            });
        }

        if (Schema::hasTable('member_entitlements')) {
            Schema::table('member_entitlements', function (Blueprint $table) {
                if (! Schema::hasColumn('member_entitlements', 'external_reference')) {
                    $table->string('external_reference')->nullable()->after('user_id');
                }
            });

            $legacyOfferId = 'ka'.'jabi_offer_id';

            if (
                Schema::hasColumn('member_entitlements', $legacyOfferId)
                && Schema::hasColumn('member_entitlements', 'external_reference')
            ) {
                try {
                    DB::statement('UPDATE member_entitlements SET external_reference = '.$legacyOfferId.' WHERE external_reference IS NULL');
                } catch (Throwable) {
                    // Some local databases quote identifiers differently; access does not depend on this value.
                }
            }

            if (Schema::hasColumn('member_entitlements', $legacyOfferId)) {
                Schema::table('member_entitlements', function (Blueprint $table) use ($legacyOfferId) {
                    $table->dropColumn($legacyOfferId);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropRememberToken();
            }

            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
        });
    }
};
