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
        Schema::create('communication_awareness', function (Blueprint $table) {
            $table->id();
            $table->string('action_initiative')->nullable(false);
            $table->enum('type', ['Internal', 'External'])->nullable(false);
            $table->string('energy_message')->nullable(false);
            $table->enum('target_audience', [
                'All Employees',
                'All office',
                'Department head',
                'Management',
            ])->nullable(false);
            $table->enum('communication', [
                'WhatsApp group',
                'Email',
                'PDF report',
            ])->nullable(false);
            $table->enum('person_in_charge', [
                'Energy Manager',
                'Facility Supervisor',
                'Compliance Offi',
                'Data Analyst',
            ])->nullable(false);
            $table->date('planned_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_awareness');
    }
};
