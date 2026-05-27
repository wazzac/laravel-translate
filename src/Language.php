<?php

namespace Wazza\DomTranslate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Wazza\DomTranslate\Database\Factories\LanguageFactory;

class Language extends Model
{
    use HasFactory;

    public static function newFactory(): LanguageFactory
    {
        return LanguageFactory::new();
    }

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'domt_languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code', 'name'
    ];

    /**
     * All Phrases for a given Language.
     */
    public function phrases(): HasMany
    {
        return $this->hasMany(Phrase::class);
    }

    /**
     * All Translations for a given Language.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
