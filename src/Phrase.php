<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Wazza\DomTranslate\Database\Factories\PhraseFactory;

class Phrase extends Model
{
    use HasFactory;

    public static function newFactory(): PhraseFactory
    {
        return PhraseFactory::new();
    }

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_phrases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hash', 'value'
    ];

    /**
     * All Translations for a given Phrase.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * The Language for the given Phrase.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * Return a count of linked Translations.
     */
    public function countTranslations(): int
    {
        return $this->translations()->count();
    }

    /**
     * Add a Translation to a Phrase.
     */
    public function addTranslation(Translation $translation): Translation
    {
        return $this->translations()->save($translation);
    }

    /**
     * Add multiple Translations to a Phrase.
     *
     * @param Translation|Collection $translations
     */
    public function addTranslations(Translation|Collection $translations): Translation|Collection
    {
        if ($translations instanceof Translation) {
            return $this->addTranslation($translations);
        }

        // it's a collection, thus call saveMany()
        return new Collection($this->translations()->saveMany($translations));
    }

    /**
     * Remove a Translation from the Phrase.
     */
    public function removeTranslation(Translation $translation): void
    {
        $translation->delete();
    }

    /**
     * Remove a Translation or collection of Translations from the Phrase.
     *
     * @param Translation|Collection $translations
     */
    public function removeTranslations(Translation|Collection $translations): void
    {
        if ($translations instanceof Translation) {
            $this->removeTranslation($translations);
            return;
        }

        // remove the collection of items
        $this->translations()->whereIn('id', $translations->pluck('id'))->delete();
    }

    /**
     * Remove all linked Translations from this Phrase.
     */
    public function removeAllTranslations(): void
    {
        $this->translations()->delete();
    }
}
