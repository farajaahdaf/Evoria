<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('e_ticket_id')->nullable()->constrained('e_tickets')->nullOnDelete();
            $table->string('ticket_code');
            $table->string('status_type', 32);
            $table->string('message');
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            $table->index(['event_id', 'scanned_at']);
            $table->index(['event_id', 'status_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_scan_logs');
    }
};
