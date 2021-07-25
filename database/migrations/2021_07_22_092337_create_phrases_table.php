<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhrasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domt_phrases', function (Blueprint $table) {
            // define tables engine and charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            // define columns
            $table->id();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('hash', 191)->nullable()->index()->comment('The hash value of the phrase'); // indexed - important!
            $table->text('value')->nullable()->comment('The phrase value');
            $table->timestamps();
            // define foreign keys
            $table->foreign('language_id')->references('id')->on('domt_languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domt_phrases');
    }
}
