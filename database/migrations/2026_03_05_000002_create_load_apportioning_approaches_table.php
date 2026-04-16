<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('load_apportioning_approaches')) {
            return;
        }

        Schema::create('load_apportioning_approaches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->boolean('is_default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Seed default approaches
        DB::table('load_apportioning_approaches')->insert([
            ['name' => 'Department', 'is_default' => true, 'created_by' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Equipment Type', 'is_default' => true, 'created_by' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Process', 'is_default' => true, 'created_by' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('load_apportioning_approaches');
    }
};
