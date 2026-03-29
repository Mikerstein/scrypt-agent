<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('content_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Regulatory Authority
            $table->string('slug'); // e.g. regulatory-authority
            $table->string('day_of_week'); // Monday, Tuesday...
            $table->text('description')->nullable();
            $table->string('primary_cta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('content_pillars'); }
};