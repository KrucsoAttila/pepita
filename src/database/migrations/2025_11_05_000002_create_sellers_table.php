<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sellers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('name');
            $table->decimal('rating', 3, 1)->default(0); // 0.0–10.0
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sellers');
    }
};
