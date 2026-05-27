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
        Schema::create('domt_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->unsignedBigInteger('phrase_id')->nullable();
            $table->text('value')->nullable()->comment('The translated phrase');
            $table->timestamps();
            $table->foreign('language_id')->references('id')->on('domt_languages');
            $table->foreign('phrase_id')->references('id')->on('domt_phrases');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domt_translations');
    }
};
