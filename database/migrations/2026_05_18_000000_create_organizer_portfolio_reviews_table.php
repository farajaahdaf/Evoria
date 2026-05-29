<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_portfolio_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_profile_id')->constrained()->cascadeOnDelete();
            $table->string('portfolio_path')->nullable();
            $table->unsignedTinyInteger('score')->default(0);
            $table->string('risk_level')->default('Incomplete');
            $table->json('breakdown')->nullable();
            $table->json('findings')->nullable();
            $table->longText('extracted_text')->nullable();
            $table->string('template_version')->default('1.0');
            $table->timestamp('analyzed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_portfolio_reviews');
    }
};
