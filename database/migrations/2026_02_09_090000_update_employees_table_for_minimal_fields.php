<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('name')->default('')->after('employee_no');
            $table->string('section')->default('')->after('department');
        });

        DB::table('employees')->update([
            'name' => DB::raw("TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')))"),
            'section' => DB::raw("COALESCE(position, '')"),
        ]);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'email', 'phone', 'position', 'date_hired']);
        });

        // Validation enforces required fields; defaults keep columns non-null without DBAL.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('employee_no');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('position')->nullable()->after('department');
            $table->date('date_hired')->nullable()->after('position');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['name', 'section']);
        });
    }
};
