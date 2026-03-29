<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('social_engagements', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('platform_post_id')->nullable();
            $table->string('platform_post_url')->nullable();
            $table->string('author_handle')->nullable();
            $table->string('author_name')->nullable();
            $table->text('original_post');
            $table->text('generated_reply')->nullable();
            $table->string('keyword_matched')->nullable();
            $table->integer('relevance_score')->default(0);
            $table->string('status')->default('pending_review');
            // pending_review, approved, rejected, published, failed
            $table->string('reply_platform_id')->nullable();
            $table->timestamp('original_posted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('social_engagements'); }
};