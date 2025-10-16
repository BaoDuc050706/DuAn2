<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 120)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district', 120)->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'ward')) {
                $table->string('ward', 120)->nullable()->after('district');
            }
            if (!Schema::hasColumn('users', 'address_line')) {
                $table->string('address_line', 255)->nullable()->after('ward');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drops = [];
            foreach (['city', 'district', 'ward', 'address_line'] as $col) {
                if (Schema::hasColumn('users', $col)) { $drops[] = $col; }
            }
            if (!empty($drops)) { $table->dropColumn($drops); }
        });
    }
};


