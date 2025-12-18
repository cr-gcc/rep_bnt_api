<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('role_has_campaigns', function (Blueprint $table) {
			$table->id();

			$table->unsignedBigInteger('role_id');
			$table->unsignedBigInteger('campaign_id');

			$table->timestamps();

			// Evitar duplicados
			$table->unique(['role_id', 'campaign_id']);

			// Foreign keys
			$table->foreign('role_id')
				->references('id')
				->on('roles')
				->cascadeOnDelete();

			$table->foreign('campaign_id')
				->references('id')
				->on('campaigns')
				->cascadeOnDelete();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('role_has_campaigns');
	}
};
