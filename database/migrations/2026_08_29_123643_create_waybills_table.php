<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybills', function (Blueprint $table) {
            $table->id();

            // Identifiers
            $table->string('reference_no')->unique();
            $table->string('waybill_no')->unique();
            $table->string('gatepass_no')->nullable();

            // Status & Timestamps
            $table->string('status')->default('draft')->index();
            $table->timestamp('loaded_at')->nullable();

            // Preparer FK (Must be NOT NULL to align with DB)
            $table->foreignId('prepared_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Vehicle & Logistics
            $table->string('vehicle_no')->nullable();
            $table->string('vehicle_owner')->nullable();
            $table->string('delivery_to')->nullable();
            $table->text('remarks')->nullable();

            // Approver Auditing
            $table->string('warehouse_approver_name')->nullable();
            $table->timestamp('warehouse_approved_at')->nullable();

            $table->string('auditor_approver_name')->nullable();
            $table->timestamp('auditor_approved_at')->nullable();

            $table->string('supply_chain_approver_name')->nullable();
            $table->timestamp('supply_chain_approved_at')->nullable();

            $table->string('final_approver_name')->nullable();
            $table->timestamp('final_approved_at')->nullable();

            // Remover Signature Data
            $table->string('remover_name')->nullable();
            $table->string('remover_signature_path')->nullable();
            $table->text('remover_signature_data')->nullable();
            $table->timestamp('remover_signed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybills');
    }
};
