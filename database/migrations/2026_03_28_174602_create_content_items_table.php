<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_pillar_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // linkedin, twitter_thread, email
            $table->string('ai_provider'); // groq, anthropic, openai
            $table->string('ai_model');
            $table->longText('prompt_used');
            $table->longText('content');
            $table->string('status')->default('draft'); // draft, approved, published, rejected
            $table->integer('performance_score')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('content_items'); }
};