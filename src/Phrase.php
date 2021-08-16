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

    /* --------------------- */
    /* -- Count ------------ */

    /**
     * Return a count of linked Translations
     *
     * @return integer
     */
    public function countTranslations()
    {
        return $this->translations()->count();
    }

    /* --------------------- */
    /* -- ADD Translation -- */

    /**
     * Add a Translation to a Phrase
     *
     * @param \Wazza\DomTranslate\Translation $translation
     * @return \Wazza\DomTranslate\Translation
     */
    public function addTranslation(Translation $translation)
    {
        return $this->translations()->save($translation);
    }

    /**
     * Add multiple Translation to the Phrase
     *
     * @param \Wazza\DomTranslate\Translation|collection $translations
     * @return \Wazza\DomTranslate\Translation
     */
    public function addTranslations($translations)
    {
        if ($translations instanceof Translation) {
            return $this->addTranslation($translations);
        }

        // it's a collection, thus call saveMany()
        return $this->translations()->saveMany($translations);
    }

    /* ------------------------ */
    /* -- REMOVE Translation -- */

    /**
     * Remove a Translation from the Phrase
     *
     * @param \Wazza\DomTranslate\Translation $translation
     * @return void
     */
    public function removeTranslation(Translation $translation)
    {
        $translation->delete();
    }

    /**
     * Remove a Translation (..or multiple Translations) from the Phrase
     *
     * @param \Wazza\DomTranslate\Translation|collection $translations
     * @return void
     */
    public function removeTranslations($translations)
    {
        if ($translations instanceof Translation) {
            $this->removeTranslation($translations);
        }

        // remove the collection of items
        $this->translations()->whereIn('id', $translations->pluck('id'))->delete();
    }

    /**
     * Remove all linked Translations from this Phrase
     *
     * @return void
     */
    public function removeAllTranslations()
    {
        $this->translations()->delete();
    }
}
