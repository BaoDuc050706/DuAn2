<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'cart_json')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('cart_json')->nullable()->after('address_line');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'cart_json')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('cart_json');
            });
        }
    }
};


