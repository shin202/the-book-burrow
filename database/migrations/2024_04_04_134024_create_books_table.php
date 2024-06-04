<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('isbn', 13)->unique();
            $table->text('description')->nullable();
            $table->text('cover_image');
            $table->integer('number_of_pages');
            $table->foreignId('publisher_id')->constrained()->cascadeOnDelete();
            $table->date('publication_date');
            $table->integer('quantity_in_stock');
            $table->decimal('price', 10);
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
