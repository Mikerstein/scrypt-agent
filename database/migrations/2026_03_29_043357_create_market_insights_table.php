<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('market_insights', function (Blueprint $table) {
            $table->id();
            $table->string('headline');
            $table->text('summary');
            $table->string('source');
            $table->string('source_url')->nullable();
            $table->string('category')->default('general');
            $table->integer('relevance_score')->default(0);
            $table->boolean('used_in_content')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('market_insights'); }
};