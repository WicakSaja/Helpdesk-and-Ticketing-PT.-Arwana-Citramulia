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
        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('assigned_by');
            $table->timestamp('assigned_at')->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_assignments', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
