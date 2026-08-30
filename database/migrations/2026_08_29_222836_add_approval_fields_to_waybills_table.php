<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waybills', function (Blueprint $table) {
            // === Delivery & Logistics Fields ===
            $table->string('vehicle_no')->nullable()->after('loaded_at');
            $table->string('vehicle_owner')->nullable()->after('vehicle_no');
            $table->string('delivery_to')->nullable()->after('vehicle_owner');
            $table->text('remarks')->nullable()->after('delivery_to');

            // === Approval Chain Fields ===
            // 1. Warehouse Manager
            $table->string('warehouse_approver_name')->nullable()->after('status');
            $table->timestamp('warehouse_approved_at')->nullable()->after('warehouse_approver_name');

            // 2. Auditor
            $table->string('auditor_approver_name')->nullable()->after('warehouse_approved_at');
            $table->timestamp('auditor_approved_at')->nullable()->after('auditor_approver_name');

            // 3. Supply Chain Manager
            $table->string('supply_chain_approver_name')->nullable()->after('auditor_approved_at');
            $table->timestamp('supply_chain_approved_at')->nullable()->after('supply_chain_approver_name');

            // 4. Final Authority
            $table->string('final_approver_name')->nullable()->after('supply_chain_approved_at');
            $table->timestamp('final_approved_at')->nullable()->after('final_approver_name');

            // === Indexes for Performance ===
            // ONLY ADD INDEXES THAT DON'T ALREADY EXIST

            // Check if index exists before creating
            // Remove this line if it already exists in original migration:
            // $table->index('status'); // ❌ REMOVE THIS - ALREADY EXISTS

            // These are new indexes that don't exist yet
            $table->index('warehouse_approved_at');
            $table->index('final_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('waybills', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['warehouse_approved_at']);
            $table->dropIndex(['final_approved_at']);

            // Drop columns
            $table->dropColumn([
                'vehicle_no',
                'vehicle_owner',
                'delivery_to',
                'remarks',
                'warehouse_approver_name',
                'warehouse_approved_at',
                'auditor_approver_name',
                'auditor_approved_at',
                'supply_chain_approver_name',
                'supply_chain_approved_at',
                'final_approver_name',
                'final_approved_at',
            ]);
        });
    }
};
