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
        Schema::table('waybills', function (Blueprint $table) {
            // Remove the existing foreign key
            $table->dropForeign(['prepared_by']);

            // Re-create it with deletion protection
            $table->foreign('prepared_by')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('waybills', function (Blueprint $table) {
            // Remove the protected foreign key
            $table->dropForeign(['prepared_by']);

            // Restore the previous relationship
            $table->foreign('prepared_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
