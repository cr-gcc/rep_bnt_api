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
		Schema::table('model_has_roles', function (Blueprint $table) {
			$table->unique(
				['model_id', 'model_type'],
				'one_role_per_model'
			);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('model_has_roles', function (Blueprint $table) {
			$table->dropUnique('one_role_per_model');
		});
	}
};
