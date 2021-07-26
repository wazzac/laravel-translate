<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // (1) create the table
        Schema::create('domt_languages', function (Blueprint $table) {
            // define tables engine and charset
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            // define columns
            $table->id();
            $table->string('code', 8)->nullable()->unique();
            $table->string('name', 30)->nullable()->index();
            $table->timestamps();
        });

        // (2) import the langs
        $languages = [
            'en' => 'English',
            'af' => 'Afrikaans',
            'zh-CN' => 'Chinese Simplefied',
            'sq' => 'Albanian',
            'ar' => 'Arabic',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'ca' => 'Catalan',
            'zh-TW' => 'Chinese Traditional',
            'cs' => 'Czech',
            'da' => 'Danish',
            'nl' => 'Dutch',
            'et' => 'Estonian',
            'tl' => 'Filipino',
            'fi' => 'Finnish',
            'fr' => 'French',
            'gl' => 'Galician',
            'de' => 'German',
            'el' => 'Greek',
            'iw' => 'Hebrew',
            'hi' => 'Hindi',
            'hu' => 'Hungarian',
            'is' => 'Icelandic',
            'id' => 'Indonesian',
            'ga' => 'Irish',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'lv' => 'Latvian',
            'lt' => 'Lithuanian',
            'mk' => 'Macedonian',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'no' => 'Norwegian',
            'fa' => 'Persian',
            'pl' => 'Polish',
            'pt' => 'Portuguese',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'sr' => 'Serbian (Cyrillic)',
            'hr' => 'Serbian (Latin)',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'es' => 'Spanish',
            'sw' => 'Swahili',
            'sv' => 'Swedish',
            'th' => 'Thai',
            'tr' => 'Turkish',
            'uk' => 'Ukrainian',
            'vi' => 'Vietnamese',
            'cy' => 'Welsh',
            'yi' => 'Yiddish',
        ];

        // loop and insert the language details (@todo - convert to a seed)
        foreach ($languages as $code => $name) {
            DB::statement("INSERT INTO `domt_languages` (`name`,`code`) VALUES ('{$code}','{$name}')");
            usleep(50000); // breathe DB, breathe... 1/20 of a second
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domt_languages');
    }
}
