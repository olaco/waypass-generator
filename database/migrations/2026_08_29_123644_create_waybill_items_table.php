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
        Schema::create('waybill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waybill_id')->constrained('waybills')->cascadeOnDelete();
            $table->enum('product_description', [
                'Glucose Classic',
                'Glucose Bulk',
                'Dequadin 100',
                'Dequadin 250',
                'Algafen Suspension',
                'Algafen 400 Tab',
                'Algafen 200 Tab',
                'Globak syrup',
                'Plexitone syrup',
                'Piriton Expectorant Child Syrup',
                'Piriton Expectorant Adult Syrup',
                'Piriton Syrup',
                'Evacid 100ml',
                'Evacid 200ml',
                'Cotrim',
                'Metronidazole',
                'Ravimal x 6',
                'Ravimal x 12',
                'Ravimal x 18',
                'Ravimal X 24',
                'Ravimal Adult',
                 'Cofta Syrup',
                 'Cofta Tablet',
                 'Neurogab',
                 'Prostagel',
                 'Manxtra 10',
                 'Manxtra 20'
                  ]);
            $table->string('batch_no');
            $table->unsignedInteger('no_of_cartons');
            $table->unsignedInteger('quantity_loaded');
            $table->index('waybill_id');
            $table->index('batch_no');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waybill_items');
    }
};
