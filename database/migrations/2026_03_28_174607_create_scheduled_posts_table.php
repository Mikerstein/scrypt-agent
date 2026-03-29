<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->string('platform'); // linkedin, twitter, email
            $table->timestamp('scheduled_at');
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('pending'); // pending, published, failed
            $table->text('error_message')->nullable();
            $table->string('platform_post_id')->nullable(); // ID returned by the platform API
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('scheduled_posts'); }
};