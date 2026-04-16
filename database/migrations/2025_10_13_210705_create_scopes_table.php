<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scopes', function (Blueprint $table) {
            $table->id();
            $table->string('scope_id')->unique();
            $table->text('included');
            $table->text('excluded');
            $table->text('rationale_for_excluding');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scopes');
    }
};
