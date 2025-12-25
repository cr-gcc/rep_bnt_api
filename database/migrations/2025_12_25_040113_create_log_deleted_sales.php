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
        Schema::create('log_deleted_sales', function (Blueprint $table) {
            $table->id();
						$table->foreignId('user_id')->constrained()->cascadeOnDelete();
						$table->string('data_base');
						$table->integer('certificate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_deleted_sales');
    }
};
