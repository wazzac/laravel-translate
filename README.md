<p align="center">
    <a href="https://github.com/wazzac/laravel-translate/releases"><img alt="Version" src="https://img.shields.io/github/v/tag/wazzac/laravel-translate?label=version&sort=semver"></a>
    <a href="https://github.com/wazzac/laravel-translate/actions"><img alt="Tests" src="https://img.shields.io/badge/tests-passing-brightgreen"></a>
    <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12.x-red?logo=laravel">
    <img alt="PHP" src="https://img.shields.io/badge/PHP-8.2%2B-blue?logo=php">
    <a href="https://github.com/wazzac/laravel-translate/blob/main/LICENSE"><img alt="License" src="https://img.shields.io/github/license/wazzac/laravel-translate"></a>
    <a href="https://github.com/wazzac/laravel-translate/issues"><img alt="Issues" src="https://img.shields.io/github/issues/wazzac/laravel-translate"></a>
    <a href="https://coff.ee/wazzac"><img alt="Buy me a coffee" src="https://img.shields.io/badge/Buy%20me%20a%20coffee-☕-yellow?style=flat&logo=buy-me-a-coffee"></a>
</p>

# Laravel Translate Package

A Laravel package that uses Blade directives to provide real-time translations for any phrase in your views — backed by a smart caching layer (session → database → cloud API) to minimise API calls and costs.

_Write your Blade templates in English, serve every visitor in their own language — automatically._

---

## Table of Contents

- [How it Works](#how-it-works)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Translation Providers](#translation-providers)
  - [Google Cloud Translation](#google-cloud-translation-setup)
  - [Bing / Azure Cognitive Translator](#bing--azure-cognitive-translator-setup)
- [Blade Directives](#blade-directives)
- [Auto-Translation (`@transl8auto`)](#auto-translation-transl8auto)
- [Language Preference API Routes](#language-preference-api-routes)
- [SetLocale Middleware](#setlocale-middleware)
- [Using the Helper Directly](#using-the-helper-directly)
- [Upgrading to v2.5](#upgrading-to-v25)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

---

## How it Works

Three database tables (`domt_languages`, `domt_phrases`, `domt_translations`) power a three-level cache:

1. **Session** _(fastest)_ — if `DOM_TRANSLATE_USE_SESSION=true`, translations are stored in the user's session after the first retrieval.
2. **Database** _(fast)_ — if `DOM_TRANSLATE_USE_DATABASE=true` _(default)_, translations are stored and retrieved from the DB. Each phrase is hashed (SHA-256 HMAC) and stored in an indexed column for sub-millisecond lookups.
3. **Cloud API** _(fallback)_ — only when no cached translation exists. The result is immediately stored in the DB (and session) to prevent future API calls.

> Laravel caches compiled Blade views, so unchanged pages make zero database or API calls after the first render.

---

## Requirements

| Requirement | Version |
|---|---|
| PHP | **^8.2** |
| Laravel | **^12.0** |
| GuzzleHTTP | `^7.8` (auto-installed) |
| Cloud API key | Google _or_ Bing (at least one) |

---

## Installation

```bash
composer require wazza/dom-translate
```

Publish the config and migrations, then run the migrations:

```bash
php artisan vendor:publish --tag="dom-translate-config"
php artisan vendor:publish --tag="dom-translate-migrations"
php artisan migrate
```

Add your API key(s) to `.env`:

```env
DOM_TRANSLATE_GOOGLE_KEY=your_google_key_here
# or
DOM_TRANSLATE_BING_KEY=your_bing_key_here
DOM_TRANSLATE_PROVIDER=bing
```

Clear config cache:

```bash
php artisan config:clear
```

> **Auto-discovery:** The service provider is auto-discovered. No manual registration is needed in `bootstrap/providers.php` unless you have disabled package discovery.

---

## Configuration

After publishing, the config file is at `config/dom_translate.php`. All settings are also controllable via `.env`:

```env
# Caching layers
DOM_TRANSLATE_USE_SESSION=false         # Cache translations in the user's session
DOM_TRANSLATE_USE_DATABASE=true         # Cache translations in the database (recommended)

# Translation provider
DOM_TRANSLATE_PROVIDER=google           # 'google' or 'bing'
DOM_TRANSLATE_GOOGLE_KEY=               # Google Cloud Translation API v2 key
DOM_TRANSLATE_BING_KEY=                 # Azure Cognitive Services subscription key

# Languages
DOM_TRANSLATE_LANG_SRC=en              # Default source language (ISO 639-1)
DOM_TRANSLATE_LANG_DEST=af             # Default destination language (ISO 639-1)

# Phrase hashing
DOM_TRANSLATE_HASH_SALT=DzBQ2DxKhNaF  # Change this — used in HMAC hash
DOM_TRANSLATE_HASH_ALGO=sha256

# Logging (0=off, 1=high, 2=mid, 3=verbose)
DOM_TRANSLATE_LOG_LEVEL=1
DOM_TRANSLATE_LOG_INDICATOR=dom-translate

# Language preference routes
DOM_TRANSLATE_ROUTES_ENABLED=true
DOM_TRANSLATE_ROUTES_PREFIX=api/translate
DOM_TRANSLATE_ROUTES_MIDDLEWARE=web

# Session/cookie key for language preference
DOM_TRANSLATE_SESSION_KEY=app_language_code

# SetLocale middleware
DOM_TRANSLATE_MIDDLEWARE_ENABLED=true
DOM_TRANSLATE_MIDDLEWARE_AUTO_APPLY=true
```

> **Security note:** Change `DOM_TRANSLATE_HASH_SALT` to a unique value per application. Do not reuse the default across projects.

---

## Translation Providers

### Google Cloud Translation Setup

1. Visit [Google Cloud Console](https://console.cloud.google.com/) and create a project.
2. Enable **Cloud Translation API** under _APIs & Services → Library_.
3. Create an API key under _APIs & Services → Credentials_.
4. Restrict the key to **Cloud Translation API** to limit exposure.
5. Add to `.env`:
   ```env
   DOM_TRANSLATE_PROVIDER=google
   DOM_TRANSLATE_GOOGLE_KEY=your_key_here
   ```

**Free tier:** 500,000 characters/month. Billing account required even for free tier.

**Test with curl:**
```bash
curl -X POST \
  "https://www.googleapis.com/language/translate/v2?key=YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"q":"Hello world","target":"fr","source":"en"}'
```

---

### Bing / Azure Cognitive Translator Setup

1. Sign in to the [Azure Portal](https://portal.azure.com/).
2. Create a **Cognitive Services** resource (or a dedicated **Translator** resource).
3. Copy one of the subscription keys from _Keys and Endpoint_.
4. Add to `.env`:
   ```env
   DOM_TRANSLATE_PROVIDER=bing
   DOM_TRANSLATE_BING_KEY=your_azure_key_here
   ```

**Free tier:** 2,000,000 characters/month (F0 tier).

---

## Blade Directives

### `@transl8(phrase[, dest[, src]])`

The primary directive. Translates a phrase to the destination language.

```blade
{{-- Use config defaults for src/dest languages --}}
<p>@transl8("I like this feature.")</p>

{{-- Specify destination --}}
<p>@transl8("We need to test it in staging.", "de")</p>

{{-- Specify both source and destination --}}
<p>@transl8("Wie weet waar Willem Wouter woon?", "en", "af")</p>
```

### Language-Specific Shortcuts

Pre-registered directives for common languages:

```blade
<p>@transl8fr("Translated to French")</p>
<p>@transl8de("Translated to German")</p>
<p>@transl8nl("Translated to Dutch")</p>
<p>@transl8es("Translated to Spanish")</p>
<p>@transl8it("Translated to Italian")</p>
<p>@transl8pt("Translated to Portuguese")</p>
<p>@transl8ru("Translated to Russian")</p>
<p>@transl8zhcn("Translated to Chinese Simplified")</p>
<p>@transl8zhtw("Translated to Chinese Traditional")</p>
<p>@transl8af("Translated to Afrikaans")</p>
<p>@transl8ar("Translated to Arabic")</p>
```

**Adding more languages** in your `AppServiceProvider::register()`:

```php
use Wazza\DomTranslate\Controllers\TranslateController;

Blade::directive('transl8ja', function ($string) {
    return "<?= app(" . TranslateController::class . "::class)->translate({$string}, 'ja', 'en'); ?>";
});
```

### `@transl8auto(phrase)`

Translates to whatever language is stored in the user's session or cookie. See [Auto-Translation](#auto-translation-transl8auto) below.

```blade
<h1>@transl8auto("Welcome to our website!")</h1>
<p>@transl8auto("This content adapts to the visitor's preferred language.")</p>
```

---

## Auto-Translation (`@transl8auto`)

`@transl8auto` is the most powerful directive. It reads the user's language preference (set via a language switcher) and translates accordingly — no per-phrase language specification needed.

### Language priority order

1. **Session** — `session('app_language_code')`
2. **Cookie** — `cookie('app_language_code')`
3. **Config default** — `dom_translate.language.dest`
4. **App locale** — `config('app.locale')`
5. **English** — final fallback

### Building a language switcher

**1. Add a selector to your layout:**

```blade
<select id="languageSelector">
    <option value="en">English</option>
    <option value="fr">FranÃ§ais</option>
    <option value="de">Deutsch</option>
    <option value="es">EspaÃ±ol</option>
    <option value="nl">Nederlands</option>
    <option value="af">Afrikaans</option>
</select>
```

**2. Wire it up with JavaScript:**

```javascript
const selector = document.getElementById('languageSelector');

// Restore current preference on load
fetch('/api/translate/get-language')
    .then(r => r.json())
    .then(data => { selector.value = data.language; });

// Persist change and reload
selector.addEventListener('change', async function () {
    await fetch('/api/translate/set-language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ language: this.value })
    });
    window.location.reload();
});
```

### Vue 3 Component Example

```vue
<template>
  <select v-model="language" @change="switchLanguage">
    <option value="en">English</option>
    <option value="fr">FranÃ§ais</option>
    <option value="de">Deutsch</option>
    <option value="es">EspaÃ±ol</option>
  </select>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const language = ref('en')

onMounted(async () => {
    const { data } = await axios.get('/api/translate/get-language')
    language.value = data.language
})

async function switchLanguage() {
    await axios.post('/api/translate/set-language', { language: language.value })
    window.location.reload()
}
</script>
```

---

## Language Preference API Routes

Enabled by default. Can be disabled with `DOM_TRANSLATE_ROUTES_ENABLED=false`.

### `POST /api/translate/set-language`

```http
POST /api/translate/set-language
Content-Type: application/json

{ "language": "fr" }
```

Response:
```json
{
    "message": "Language preference set successfully.",
    "language": "fr"
}
```

Also sets a 1-year, `httpOnly` cookie with the preference.

### `GET /api/translate/get-language`

```http
GET /api/translate/get-language
```

Response:
```json
{ "language": "fr" }
```

### Disable routes and roll your own

```env
DOM_TRANSLATE_ROUTES_ENABLED=false
```

```php
use Wazza\DomTranslate\Helpers\TranslateHelper;

class LanguageController extends Controller
{
    public function set(Request $request)
    {
        $request->validate(['language' => 'required|string|min:2|max:5']);
        return TranslateHelper::setLanguage($request->input('language'));
    }

    public function get()
    {
        return TranslateHelper::getLanguage();
    }
}
```

---

## SetLocale Middleware

`SetLocaleMiddleware` automatically syncs Laravel's application locale — and Carbon's locale — to the user's language preference on every request.

This ensures `__()`, `trans()`, `@lang`, validation messages, and Carbon date formatting all use the same language as the translate directives.

**Enabled by default.** To disable:

```env
DOM_TRANSLATE_MIDDLEWARE_ENABLED=false
```

To keep the middleware but not auto-apply it to the `web` group:

```env
DOM_TRANSLATE_MIDDLEWARE_AUTO_APPLY=false
```

Then apply it manually where needed:

```php
// routes/web.php
Route::middleware(['web', 'dom-translate.locale'])->group(function () {
    // your routes
});
```

---

## Using the Helper Directly

`TranslateHelper` and `PhraseHelper` are also available for use in controllers, jobs, or anywhere outside Blade:

```php
use Wazza\DomTranslate\Helpers\TranslateHelper;
use Wazza\DomTranslate\Controllers\TranslateController;

// Translate directly
$translated = app(TranslateController::class)->translate('Hello world', 'fr', 'en');

// Auto-translate using current user's preference
$translated = TranslateHelper::autoTransl8('Hello world');

// Get current user's language code
$lang = TranslateHelper::currentDefinedLanguageCode();

// Set language preference (returns JsonResponse with cookie)
return TranslateHelper::setLanguage('de');
```

---

## Upgrading to v2.5

If you are upgrading from v2.4.x:

1. **Re-publish migrations** — the migration files have been updated to anonymous class syntax (required by Laravel 12). If you have already run the old migrations, no action is needed for existing databases. For fresh installs, re-publish:
   ```bash
   php artisan vendor:publish --tag="dom-translate-migrations" --force
   php artisan migrate
   ```

2. **No breaking changes** — all Blade directives, config keys, and `.env` variables remain identical.

3. **BingTranslate is now implemented** — if you set `DOM_TRANSLATE_PROVIDER=bing` in your `.env`, it will now work with the Azure Cognitive Translator v3 API.

4. **Cookie security** — the language preference cookie is now `httpOnly=true` (XSS protection). This is transparent to your application.

5. **`DOM_TRANSLATE_HASH_SALT`** — if you are using a custom salt, ensure it remains set in your `.env` to preserve existing hash lookups in the database.

---

## Troubleshooting

### `E_STRICT` deprecation warnings during `composer install`

```
Deprecation Notice: Constant E_STRICT is deprecated in /usr/share/php/Composer/Util/Silencer.php
```

This is **not** caused by this package. It is caused by an outdated system-level Composer installation (typically `/usr/share/php/Composer/`). Update your server's Composer:

```bash
sudo composer self-update
# or, on Debian/Ubuntu:
sudo apt-get install --only-upgrade composer
```

### Translations not showing / returning original phrase

1. Check your API key is set: `DOM_TRANSLATE_GOOGLE_KEY` or `DOM_TRANSLATE_BING_KEY`.
2. Check `DOM_TRANSLATE_LOG_LEVEL=3` and review Laravel's log for API errors.
3. Ensure the destination language code (`DOM_TRANSLATE_LANG_DEST`) exists in the `domt_languages` table.
4. Clear config and view cache:
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

### `MissingAppKeyException` in tests

Ensure `APP_KEY` is set in `phpunit.xml` or your `.env.testing`. The package's test suite sets a dummy key automatically for its own tests.

### Language not persisting between requests

Ensure `DOM_TRANSLATE_USE_SESSION=true` or that cookies are being sent with requests. For AJAX requests, verify the `X-CSRF-TOKEN` header is included in `POST` calls.

---

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request. For security vulnerabilities, see [SECURITY.md](SECURITY.md).

---

<p align="center">Made with ☕ by <a href="https://www.wazzac.dev">Warren Coetzee</a></p>
