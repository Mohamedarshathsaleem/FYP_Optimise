<?php
// database/migrations/xxxx_xx_xx_create_swot_analyses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('swot_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('swot_id')->unique(); // SW001, SW002, etc.
            $table->string('title')->nullable(); // Optional title for the analysis
            $table->text('strengths'); // Internal positive factors
            $table->text('weaknesses'); // Internal negative factors
            $table->text('opportunities'); // External positive factors
            $table->text('threats'); // External negative factors
            $table->string('status')->default('Active'); // Active, Archived, Draft
            $table->string('created_by')->nullable(); // User who created
            $table->string('approved_by')->nullable(); // User who approved
            $table->datetime('approved_at')->nullable();
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('swot_analyses');
    }
};
