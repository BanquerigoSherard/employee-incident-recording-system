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
            $table->string('first_name')->after('employee_no');
            $table->string('last_name')->after('first_name');
        });

        DB::table('employees')->update([
            'first_name' => DB::raw("TRIM(SUBSTRING_INDEX(name, ' ', 1))"),
            'last_name' => DB::raw("TRIM(CASE WHEN LOCATE(' ', name) > 0 THEN SUBSTRING(name, LOCATE(' ', name) + 1) ELSE '' END)"),
        ]);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('name')->after('employee_no');
        });

        DB::table('employees')->update([
            'name' => DB::raw("TRIM(CONCAT(first_name, ' ', last_name))"),
        ]);

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
