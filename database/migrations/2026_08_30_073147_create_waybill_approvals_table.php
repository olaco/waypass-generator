<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waybill_approvals', function (Blueprint $table) {
            $table->id();

            // Foreign key to waybill
            $table->foreignId('waybill_id')
                ->constrained('waybills')
                ->cascadeOnDelete();

            // Approver information
            $table->foreignId('approver_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Approval stage
            $table->enum('stage', [
                'warehouse_manager',
                'auditor',
                'supply_chain_manager',
                'final_authority'
            ]);

            // Approval status
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])
                ->default('pending');

            // Signature storage
            $table->string('signature_path')->nullable();
            $table->text('signature_data')->nullable();

            // Additional metadata
            $table->text('remarks')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            // Timestamps
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['waybill_id', 'stage']);
            $table->index('status');
            $table->index('approved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waybill_approvals');
    }
};
