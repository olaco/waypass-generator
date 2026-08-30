<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waybills', function (Blueprint $table) {
            // === Remover (Loader/Unloader) Fields ===
            $table->string('remover_name')->nullable()->after('final_approved_at');
            $table->string('remover_signature_path')->nullable()->after('remover_name');
            $table->text('remover_signature_data')->nullable()->after('remover_signature_path');
            $table->timestamp('remover_signed_at')->nullable()->after('remover_signature_data');
        });
    }

    public function down(): void
    {
        Schema::table('waybills', function (Blueprint $table) {
            $table->dropColumn([
                'remover_name',
                'remover_signature_path',
                'remover_signature_data',
                'remover_signed_at',
            ]);
        });
    }
};
