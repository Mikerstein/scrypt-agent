<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('title')->nullable(); // e.g. Head of Treasury
            $table->string('segment')->nullable(); // hedge_fund, bank, family_office, fintech, web3
            $table->string('source')->nullable(); // twitter, linkedin, email, referral
            $table->string('source_content_id')->nullable();
            $table->string('status')->default('new'); // new, contacted, qualified, meeting, closed, lost
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('leads'); }
};