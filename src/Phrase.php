<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Model;
use Wazza\DomTranslate\Translation;
use Wazza\DomTranslate\Language;

class Phrase extends Model
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_phrases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hash', 'value'
    ];

    /**
     * Method to return a 'one-to-many' reltionship. All Translations for a given Phrase
     * @return type
     */
    public function translations()
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Method to return a `many-to-one` relationship. Showing the Language for the given Phrase
     * @return type
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
