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
        Schema::create('risks_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('risk_id')->unique(); // Auto-generated ID like RO001, RO002
            $table->text('issue'); // The issue description
            $table->enum('type', ['Internal', 'External']);
            $table->enum('category', ['Risk', 'Opportunity']);
            $table->integer('likelihood'); // 1-5 scale
            $table->enum('risk_level', ['Low', 'Medium', 'High']);
            $table->text('impact_description')->nullable(); // Impact on EnMS
            $table->json('mitigation_actions')->nullable(); // JSON array of actions
            $table->string('responsible_person')->nullable();
            $table->date('review_date')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Reject'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index('risk_level');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks_opportunities');
    }
};
