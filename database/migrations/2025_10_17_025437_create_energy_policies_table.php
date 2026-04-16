<?php
// database/migrations/xxxx_xx_xx_create_energy_policies_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('energy_policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->json('commitments')->nullable(); // Store commitments as JSON
            $table->longText('policy_statement')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('energy_standard')->nullable();
            $table->string('document_path')->nullable();
            $table->boolean('policy_completed')->default(false);
            $table->date('date_completed')->nullable();
            $table->date('date_approved')->nullable();
            $table->string('who_approved')->nullable();
            $table->enum('status', ['draft', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_policies');
    }
};
