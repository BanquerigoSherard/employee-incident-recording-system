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
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'incident_time',
                'location',
                'incident_type',
                'severity',
                'immediate_action',
                'reported_by',
                'witnesses',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->time('incident_time')->nullable()->after('incident_date');
            $table->string('location')->after('incident_time');
            $table->string('incident_type')->after('location');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->after('incident_type');
            $table->text('immediate_action')->nullable()->after('description');
            $table->string('reported_by')->after('immediate_action');
            $table->text('witnesses')->nullable()->after('reported_by');
            $table->enum('status', ['open', 'in_review', 'closed'])->default('open')->after('witnesses');
        });
    }
};
