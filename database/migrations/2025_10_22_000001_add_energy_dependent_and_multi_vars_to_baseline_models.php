<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if columns already exist (e.g., table was manually modified)
        if (Schema::hasColumn('baseline_models', 'dependent_variable_type')) {
            return;
        }

        Schema::table('baseline_models', function (Blueprint $table) {
            // Dependent variable type + FK columns
            $table->enum('dependent_variable_type', ['energy_data', 'energy_resource', 'monthly_variable'])
                  ->default('monthly_variable')
                  ->after('dependent_variable');

            $table->unsignedBigInteger('energy_data_id')->nullable()->after('dependent_variable_type');
            $table->unsignedBigInteger('energy_resource_id')->nullable()->after('energy_data_id');

            // Independent variable type columns for X1 and X2 (were missing in original schema)
            $table->enum('independent_variable_type_x1', ['monthly_production', 'monthly_variable'])
                  ->nullable()
                  ->after('independent_variable_x1');
            $table->unsignedBigInteger('monthly_production_id_x1')->nullable()->after('independent_variable_type_x1');
            $table->unsignedBigInteger('monthly_variable_id_x1')->nullable()->after('monthly_production_id_x1');

            $table->enum('independent_variable_type_x2', ['monthly_production', 'monthly_variable'])
                  ->nullable()
                  ->after('independent_variable_x2');
            $table->unsignedBigInteger('monthly_production_id_x2')->nullable()->after('independent_variable_type_x2');
            $table->unsignedBigInteger('monthly_variable_id_x2')->nullable()->after('monthly_production_id_x2');

            // X3 independent variable
            $table->string('independent_variable_x3')->nullable()->after('monthly_variable_id_x2');
            $table->enum('independent_variable_type_x3', ['monthly_production', 'monthly_variable'])
                  ->nullable()
                  ->after('independent_variable_x3');
            $table->unsignedBigInteger('monthly_production_id_x3')->nullable()->after('independent_variable_type_x3');
            $table->unsignedBigInteger('monthly_variable_id_x3')->nullable()->after('monthly_production_id_x3');

            // X4 independent variable
            $table->string('independent_variable_x4')->nullable()->after('monthly_variable_id_x3');
            $table->enum('independent_variable_type_x4', ['monthly_production', 'monthly_variable'])
                  ->nullable()
                  ->after('independent_variable_x4');
            $table->unsignedBigInteger('monthly_production_id_x4')->nullable()->after('independent_variable_type_x4');
            $table->unsignedBigInteger('monthly_variable_id_x4')->nullable()->after('monthly_production_id_x4');

            // Foreign keys
            $table->foreign('energy_data_id')->references('id')->on('energy_data')->onDelete('cascade');
            $table->foreign('energy_resource_id')->references('id')->on('energy_resource_data')->onDelete('cascade');
            $table->foreign('monthly_production_id_x1')->references('id')->on('monthly_productions')->onDelete('set null');
            $table->foreign('monthly_variable_id_x1')->references('id')->on('monthly_variables')->onDelete('set null');
            $table->foreign('monthly_production_id_x2')->references('id')->on('monthly_productions')->onDelete('set null');
            $table->foreign('monthly_variable_id_x2')->references('id')->on('monthly_variables')->onDelete('set null');
            $table->foreign('monthly_production_id_x3')->references('id')->on('monthly_productions')->onDelete('set null');
            $table->foreign('monthly_variable_id_x3')->references('id')->on('monthly_variables')->onDelete('set null');
            $table->foreign('monthly_production_id_x4')->references('id')->on('monthly_productions')->onDelete('set null');
            $table->foreign('monthly_variable_id_x4')->references('id')->on('monthly_variables')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('baseline_models', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['energy_data_id']);
            $table->dropForeign(['energy_resource_id']);
            $table->dropForeign(['monthly_production_id_x1']);
            $table->dropForeign(['monthly_variable_id_x1']);
            $table->dropForeign(['monthly_production_id_x2']);
            $table->dropForeign(['monthly_variable_id_x2']);
            $table->dropForeign(['monthly_production_id_x3']);
            $table->dropForeign(['monthly_variable_id_x3']);
            $table->dropForeign(['monthly_production_id_x4']);
            $table->dropForeign(['monthly_variable_id_x4']);

            // Drop columns
            $table->dropColumn([
                'dependent_variable_type',
                'energy_data_id',
                'energy_resource_id',
                'independent_variable_type_x1',
                'monthly_production_id_x1',
                'monthly_variable_id_x1',
                'independent_variable_type_x2',
                'monthly_production_id_x2',
                'monthly_variable_id_x2',
                'independent_variable_x3',
                'independent_variable_type_x3',
                'monthly_production_id_x3',
                'monthly_variable_id_x3',
                'independent_variable_x4',
                'independent_variable_type_x4',
                'monthly_production_id_x4',
                'monthly_variable_id_x4',
            ]);
        });
    }
};
