<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->string('stakeholder_id')->unique(); // ST-04, etc
            $table->string('name');
            $table->enum('type', ['Internal', 'External']);
            $table->string('role');
            $table->text('needs_expectations');
            $table->enum('influence_level', ['Low', 'Medium', 'High']);
            $table->string('communication_method');
            $table->string('engagement_frequency');
            $table->string('responsible_person');
            $table->text('remarks');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stakeholders');
    }
};
