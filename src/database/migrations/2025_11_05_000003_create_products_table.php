<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('title');
            $table->text('brand');
            $table->uuid('category_id');
            $table->decimal('price', 10, 2);
            $table->text('currency');         // pl. HUF/EUR/USD
            $table->integer('stock')->default(0);
            $table->uuid('seller_id');
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('popularity')->default(0);
            $table->jsonb('attributes');       // PG JSONB
            $table->timestamps();

            $table->index('category_id');
            $table->index('seller_id');
            $table->index('brand');
            $table->index('price');
            $table->index('rating');
            $table->index('popularity');
            $table->index('created_at');
        });

        // GIN index JSONB-re (PostgreSQL)
        DB::statement('CREATE INDEX products_attributes_gin ON products USING GIN (attributes)');
    }

    public function down(): void {
        // Biztonság kedvéért, ha az index még létezik
        try { DB::statement('DROP INDEX IF EXISTS products_attributes_gin'); } catch (\Throwable $e) {}
        Schema::dropIfExists('products');
    }
};
