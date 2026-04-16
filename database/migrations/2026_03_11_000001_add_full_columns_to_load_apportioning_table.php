<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrate load_apportioning to the full SEU-based schema.
     *
     * Handles two possible prior states:
     *  A) Minimal schema (id, approach_id, name, timestamps)
     *  B) arshath-30-01-26 refactor schema (adds year, energy_type_id,
     *     unit_mode, row_label, energy_consumption_gj, load_percentage)
     *
     * Both are brought up to the full schema the model/controller expects.
     */
    public function up(): void
    {
        // ── Step 1: Drop legacy composite indexes (ignore if they don't exist) ──
        foreach (['la_unique_row', 'la_filter_idx'] as $idx) {
            try {
                Schema::table('load_apportioning', function (Blueprint $table) use ($idx) {
                    $table->dropIndex($idx);
                });
            } catch (\Exception $e) {
                // Index doesn't exist — safe to ignore
            }
        }

        // ── Step 2: Drop legacy FK + column: energy_type_id ──
        if (Schema::hasColumn('load_apportioning', 'energy_type_id')) {
            try {
                Schema::table('load_apportioning', function (Blueprint $table) {
                    $table->dropForeign(['energy_type_id']);
                });
            } catch (\Exception $e) {
                // FK may already be gone
            }
            Schema::table('load_apportioning', function (Blueprint $table) {
                $table->dropColumn('energy_type_id');
            });
        }

        // ── Step 3: Drop other legacy columns ──
        foreach (['unit_mode', 'row_label', 'energy_consumption_gj', 'load_percentage', 'name'] as $col) {
            if (Schema::hasColumn('load_apportioning', $col)) {
                Schema::table('load_apportioning', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        // ── Step 4: Add all columns required by the current model/controller ──
        Schema::table('load_apportioning', function (Blueprint $table) {
            if (!Schema::hasColumn('load_apportioning', 'seu_category')) {
                $table->string('seu_category', 50)->nullable()
                    ->comment('Energy type ID (e.g., edata_1, rdata_2)');
            }

            if (!Schema::hasColumn('load_apportioning', 'submeter_reference')) {
                $table->string('submeter_reference', 100)->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'equipment_type')) {
                $table->string('equipment_type', 100)->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'equipment_name')) {
                $table->string('equipment_name', 150)->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'equipment_remark')) {
                $table->text('equipment_remark')->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'electricity_load_gj')) {
                $table->decimal('electricity_load_gj', 14, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'electricity_load_pct')) {
                $table->decimal('electricity_load_pct', 8, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'ng_meter_reference')) {
                $table->string('ng_meter_reference', 100)->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'ng_load_gj')) {
                $table->decimal('ng_load_gj', 14, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'ng_load_pct')) {
                $table->decimal('ng_load_pct', 8, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'total_energy_gj')) {
                $table->decimal('total_energy_gj', 14, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'total_energy_pct')) {
                $table->decimal('total_energy_pct', 8, 4)->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'calculation_remark')) {
                $table->text('calculation_remark')->nullable();
            }

            if (!Schema::hasColumn('load_apportioning', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }

            if (!Schema::hasColumn('load_apportioning', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('load_apportioning', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        // ── Step 5: Add index on seu_category (ignore if it already exists) ──
        if (Schema::hasColumn('load_apportioning', 'seu_category')) {
            try {
                Schema::table('load_apportioning', function (Blueprint $table) {
                    $table->index('seu_category');
                });
            } catch (\Exception $e) {
                // Index already exists — safe to ignore
            }
        }
    }

    public function down(): void
    {
        Schema::table('load_apportioning', function (Blueprint $table) {
            $toDrop = [
                'seu_category', 'submeter_reference', 'equipment_type',
                'equipment_name', 'equipment_remark',
                'electricity_load_gj', 'electricity_load_pct',
                'ng_meter_reference', 'ng_load_gj', 'ng_load_pct',
                'total_energy_gj', 'total_energy_pct',
                'calculation_remark', 'sort_order',
            ];
            foreach ($toDrop as $col) {
                if (Schema::hasColumn('load_apportioning', $col)) {
                    $table->dropColumn($col);
                }
            }

            if (Schema::hasColumn('load_apportioning', 'created_by')) {
                try { $table->dropForeign(['created_by']); } catch (\Exception $e) {}
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('load_apportioning', 'updated_by')) {
                try { $table->dropForeign(['updated_by']); } catch (\Exception $e) {}
                $table->dropColumn('updated_by');
            }
        });
    }
};
