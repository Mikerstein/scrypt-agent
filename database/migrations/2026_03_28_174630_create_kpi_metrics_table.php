<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_type'); // leads, meetings, conversions, impressions, engagement
            $table->string('platform')->nullable(); // twitter, linkedin, email, overall
            $table->integer('value')->default(0);
            $table->date('recorded_date');
            $table->string('period')->default('daily'); // daily, weekly, monthly
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kpi_metrics'); }
};