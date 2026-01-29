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
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // This acts as your unique 'item_id' automatically
            $table->string('name'); // item_name
            $table->string('measurement_unit'); // 'Kg', 'cm', 'packs'
            $table->decimal('quantity', 10, 2)->default(0); // Allows 1.50 Kg or 10.00 Packs
            $table->string('image_path')->nullable(); // Stores the file name. Nullable means optional.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
