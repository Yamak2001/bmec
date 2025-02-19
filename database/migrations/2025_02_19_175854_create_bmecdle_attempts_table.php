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
        Schema::create('bmecdle_attempts', function (Blueprint $table) {
            $table->id();

            // Relationship to bmec_worlde_terms
            $table->unsignedBigInteger('bmec_term_id');
            $table->string('bmec_term');   // Storing the actual term
            $table->string('bmedle_day');  // E.g., day or puzzle identifier

            // Which user is attempting
            $table->unsignedBigInteger('attempted_by'); // references user ID

            // Timestamps for puzzle interactions
            $table->dateTime('started_at')->nullable();    // user started
            $table->dateTime('completed_at')->nullable();  // user finished or gave up

            // Up to five attempts
            $table->string('attempt_one')->nullable();
            $table->string('attempt_two')->nullable();
            $table->string('attempt_three')->nullable();
            $table->string('attempt_four')->nullable();
            $table->string('attempt_five')->nullable();

            // Additional metrics
            $table->integer('attempts_used')->default(0);
            $table->boolean('did_not_finish')->default(false);
            $table->boolean('hint_used')->default(false);

            // Duration and status
            $table->integer('duration_of_attempt')->nullable();
            $table->string('attempt_status')->nullable(); // e.g. "solved_on_first"
            $table->integer('attempt_score')->nullable();

            $table->timestamps();

            // Index for performance on frequent queries
            $table->index(['bmec_term_id', 'attempted_by', 'bmedle_day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmecdle_attempts');
    }
};
