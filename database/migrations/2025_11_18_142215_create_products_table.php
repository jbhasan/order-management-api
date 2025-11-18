<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create('products', function (Blueprint $table) {
			$table->id();
			$table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
			$table->string('name');
			$table->text('description')->nullable();
			$table->string('sku')->unique();
			$table->decimal('price', 10, 2);
			$table->boolean('is_active')->default(true);
			$table->json('attributes')->nullable(); // For storing product attributes like color, size, etc.
			$table->string('image_url')->nullable();
			$table->timestamps();
			$table->softDeletes();

			// Indexes for search optimization
			$table->index('name');
			$table->index('sku');
			$table->index('vendor_id');
			$table->index('is_active');
		});

		// Add fulltext index only for MySQL/PostgreSQL (not SQLite)
		$driver = DB::connection()->getDriverName();
		if (in_array($driver, ['mysql', 'pgsql'])) {
			Schema::table('products', function (Blueprint $table) {
				$table->fullText(['name', 'description']); // Full-text search
			});
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('products');
	}
};
