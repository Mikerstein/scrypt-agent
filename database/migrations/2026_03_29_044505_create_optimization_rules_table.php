<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('optimization_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_type'); // hook, cta, tone, topic, format
            $table->string('platform')->nullable(); // linkedin, twitter, email, all
            $table->text('instruction');
            $table->integer('weight')->default(1);
            $table->boolean('is_active')->default(true);
            $table->string('source')->default('manual'); // manual, ai_generated
            $table->decimal('confidence_score', 5, 2)->default(0.00);
            $table->text('evidence')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('optimization_rules'); }
};