<?php
// database/migrations/xxxx_xx_xx_create_energy_resource_data_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('energy_resource_data', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type');
            $table->string('provider');
            $table->string('account_no');
            $table->string('contract_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_resource_data');
    }
};
