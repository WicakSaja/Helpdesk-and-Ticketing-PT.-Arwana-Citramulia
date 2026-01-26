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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('ticket_number')->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('status_id')->constrained('ticket_statuses');
            $table->string('subject');
            $table->text('description');
            $table->string('channel'); // web, email, phone
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['status_id', 'requester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
