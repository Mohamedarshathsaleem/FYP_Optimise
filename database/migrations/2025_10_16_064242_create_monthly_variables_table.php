<?php
// database/migrations/xxxx_xx_xx_create_monthly_variables_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_variables', function (Blueprint $table) {
            $table->id();
            $table->string('variable_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_variables');
    }
};
