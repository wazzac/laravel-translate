<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wazza\DomTranslate\Database\Factories\TranslationFactory;

class Translation extends Model
{
    use HasFactory;

    public static function newFactory(): TranslationFactory
    {
        return TranslationFactory::new();
    }

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'value'
    ];

    /**
     * The Language for the given Translation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    /**
     * The Phrase for the given Translation.
     */
    public function phrase(): BelongsTo
    {
        return $this->belongsTo(Phrase::class, 'phrase_id', 'id');
    }

    /**
     * Return a count of Translations.
     */
    public function countTranslations(): int
    {
        return $this->count();
    }
}
