<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SEU Criteria/Configuration table
        Schema::create('seu_criteria', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year');
            $table->string('criteria_type', 50)->default('load_percentage')
                ->comment('load_percentage, absolute_gj, custom');
            $table->decimal('upper_limit', 8, 4)->default(1.0000)
                ->comment('Upper threshold (e.g., 1.0 = 100%)');
            $table->decimal('lower_limit', 8, 4)->default(0.0500)
                ->comment('Lower threshold (e.g., 0.05 = 5%)');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['year', 'criteria_type']);
            $table->index('year');
        });

        // SEU Flagged Items table
        Schema::create('seu_flagging', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year');
            $table->foreignId('criteria_id')->constrained('seu_criteria')->cascadeOnDelete();

            // SEU Identification
            $table->string('seu_name', 150)->comment('Name of the SEU');
            $table->enum('energy_type', ['energy', 'energy_resource'])
                ->comment('Whether this is an Energy or Energy Resource SEU');

            // Performance Data
            $table->decimal('current_gj', 14, 4)->comment('Current energy consumption in GJ');
            $table->decimal('overall_usage_pct', 8, 4)->comment('Percentage of overall usage');
            $table->string('enpi_reference', 100)->nullable()
                ->comment('Associated EnPI (e.g., GJ/tonne of production)');

            // Flagging Status
            $table->boolean('is_flagged')->default(true)->comment('Whether this item is flagged as SEU');
            $table->boolean('is_manually_overridden')->default(false)
                ->comment('Whether user manually changed the flag status');
            $table->text('override_reason')->nullable();

            // Source Reference
            $table->foreignId('load_apportioning_id')->nullable()
                ->constrained('load_apportioning')->nullOnDelete()
                ->comment('Source record from load apportioning');

            // Metadata
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['year', 'energy_type']);
            $table->index(['year', 'is_flagged']);
            $table->index('seu_name');
        });

        // SEU Action Items (for tracking improvements)
        Schema::create('seu_action_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seu_flagging_id')->constrained('seu_flagging')->cascadeOnDelete();
            $table->string('action_description', 500);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('target_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->decimal('estimated_savings_gj', 14, 4)->nullable();
            $table->decimal('actual_savings_gj', 14, 4)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seu_action_items');
        Schema::dropIfExists('seu_flagging');
        Schema::dropIfExists('seu_criteria');
    }
};
