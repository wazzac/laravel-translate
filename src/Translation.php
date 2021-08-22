<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Model;
use Wazza\DomTranslate\Phrase;
use Wazza\DomTranslate\Language;

class Translation extends Model
{
    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value'
    ];

    /**
     * Method to return a `many-to-one` relationship. Showing the Language for the given Translation
     * @return type
     */
    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * Method to return a `many-to-one` relationship. Showing the Phrase for the given Translation
     * @return type
     */
    public function phrase()
    {
        return $this->belongsTo(Phrase::class, 'phrase_id', 'id');
    }

    /* --------------------- */
    /* -- Count ------------ */

    /**
     * Return a count of Translations
     *
     * @return integer
     */
    public function countTranslations()
    {
        return $this->count();
    }
}
