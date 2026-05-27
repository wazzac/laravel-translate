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
        Schema::create('domt_phrases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('hash', 191)->nullable()->index()->comment('The hash value of the phrase');
            $table->text('value')->nullable()->comment('The phrase value');
            $table->timestamps();
            $table->foreign('language_id')->references('id')->on('domt_languages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domt_phrases');
    }
};
