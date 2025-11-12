<?php
// File: database/migrations/2025_11_10_000000_create_reindex_jobs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reindex_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->index(); // pending|running|completed|failed
            $table->boolean('recreate')->default(false);
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reindex_jobs');
    }
};
