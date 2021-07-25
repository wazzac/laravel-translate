<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domt_translations', function (Blueprint $table) {
            // define tables engine and charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            // define columns
            $table->id();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->unsignedBigInteger('phrase_id')->nullable();
            $table->text('value')->nullable()->comment('The translated phrase');
            $table->timestamps();
            // define foreign keys
            $table->foreign('language_id')->references('id')->on('domt_languages');
            $table->foreign('phrase_id')->references('id')->on('domt_phrases');
            // add some indexes
            // none; fk will have their own indexes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domt_translations');
    }
}
