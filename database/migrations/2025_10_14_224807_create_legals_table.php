<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('legals', function (Blueprint $table) {
            $table->id();
            $table->string('legal_id')->unique();
            $table->string('title');
            $table->string('authority');
            $table->string('relevant_clause');
            $table->string('reference_others');
            $table->enum('category', ['Legal', 'Regulatory', 'Standard']);
            $table->date('effective_date');
            $table->enum('relevant', ['Y', 'N']);
            $table->text('description');
            $table->string('what_affected');
            $table->text('action_required');
            $table->string('responsible_person');
            $table->date('last_review_date')->nullable();
            $table->enum('review_frequency', ['Monthly', 'Quarterly', 'Annually', 'Bi-annually']);
            $table->enum('further_action_bool', ['Yes', 'No']);
            $table->text('further_action')->nullable();
            $table->enum('compliance_status', ['Compliant', 'In Progress', 'Non-Compliant', 'Not Applicable']);
            $table->text('evidence_compliance');
            $table->text('remarks');
            $table->timestamps();

            // Indexes for better performance
            $table->index('category');
            $table->index('relevant');
            $table->index('compliance_status');
            $table->index('effective_date');
            $table->index('last_review_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('legals');
    }
};
