<?php
// database/migrations/xxxx_xx_xx_create_monthly_productions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_productions', function (Blueprint $table) {
            $table->id();
            $table->string('production_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_productions');
    }
};
