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
        Schema::create('bmec_worlde_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term');
            $table->string('hints');
            $table->boolean('is_used')->default(false);
            $table->dateTime('usage_date')->nullable();
            $table->integer('viewed_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmec_worlde_terms');
    }
};
