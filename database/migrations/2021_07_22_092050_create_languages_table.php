<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // (1) create the table
        Schema::create('domt_languages', function (Blueprint $table) {
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

        // loop and insert the language details
        $currentDate = Carbon::now();
        foreach ($languages as $code => $name) {
            DB::table('domt_languages')->insert([
                'code'       => $code,
                'name'       => $name,
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domt_languages');
    }
};
