<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Model;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Phrase;

class Language extends Model
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name'
    ];

    /**
     * Method to return a 'one-to-many' relationship. All Phrases for a given Language
     * @return type
     */
    public function phrases()
    {
        return $this->hasMany(Phrase::class);
    }

    /**
     * Method to return a 'one-to-many' relationship. All Translations for a given Language
     * @return type
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }
}
