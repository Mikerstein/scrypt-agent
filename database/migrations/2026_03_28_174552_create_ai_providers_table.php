<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // anthropic, openai, groq
            $table->string('model');
            $table->boolean('is_active')->default(true);
            $table->integer('tokens_used')->default(0);
            $table->integer('requests_made')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ai_providers'); }
};