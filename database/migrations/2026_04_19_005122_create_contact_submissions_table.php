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
		Schema::create('contact_submissions', function (Blueprint $table) {
			$table->uuid()->primary();
			$table->string('name')->required();
			$table->string('phone')->nullable();
			$table->string('email')->required();
			$table->string('subject')->required();
			$table->text('message')->required();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('contact_submissions');
	}
};
