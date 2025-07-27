<p align="center">
    <a href="https://github.com/wazzac/domTranslate/issues"><img alt="GitHub issues" src="https://img.shields.io/github/issues/wazzac/domTranslate"></a>
    <a href="https://github.com/WarrenGIT/domTranslate/stargazers"><img alt="GitHub stars" src="https://img.shields.io/github/stars/wazzac/domTranslate"></a>
    <a href="https://github.com/WarrenGIT/domTranslate/blob/main/LICENSE"><img alt="GitHub license" src="https://img.shields.io/github/license/WarrenGIT/domTranslate"></a>
    <a href="https://github.com/WarrenGIT/domTranslate"><img alt="GitHub version" src="https://img.shields.io/github/v/tag/WarrenGIT/domTranslate?label=version&sort=semver"></a>
</p>


# Laravel Translate Package

A library that leverages Laravel Directives to provide automated translations for all your Blade phrases or words.

_Example: Write HTML static data in English and display it in a different language in real-time._

## Overview

The library uses three database tables (_domt_phrases_, _domt_translations_, and _domt_languages_) to manage translations efficiently.

1.  On page load, the system searches for a specific translation using the provided phrase in the `@transl8()` directive from the _domt_translations_ table.
    > Laravel generally cache views, so if the content of the entire page didn't change, steps 1 - 4 will not fire as the cached view will simply load.
2.  If the translation is found, it is returned and displayed on the page without making an API call.
3.  If the translation is not found _(not translated yet)_, the Google Translate API (or another defined provider) is called to retrieve the new translation.
4.  The newly translated text is then inserted into the database to avoid future API calls for the same phrase.

> Note: To ensure quick retrieval of translations, each phrase is hashed and stored in an indexed table column. All searches are performed against this indexed column for optimal performance.

## Installation

> PHP 8.0 is the minimum requirement for this project.

Follow these steps to install the package:

```bash
composer require wazza/dom-translate
php artisan vendor:publish --tag="dom-translate-config"
php artisan vendor:publish --tag="dom-translate-migrations"
php artisan migrate
```

Register the Service Provider (if not auto-discovered):
Add to `bootstrap/providers.php`:

```php
return [
        App\Providers\AppServiceProvider::class,
        Wazza\DomTranslate\Providers\DomTranslateServiceProvider::class,
];
```

> _If your package supports Laravel auto-discovery, this step may be optional._

Add `DOM_TRANSLATE_GOOGLE_KEY={your_google_api_key}` to your `.env` file and run:

```bash
php artisan config:clear
php artisan config:cache
```

Below are all the supported `.env` keys with their default values if not provided. The `KEY` (i.e., `DOM_TRANSLATE_GOOGLE_KEY`) is required.

```
DOM_TRANSLATE_USE_SESSION=true
DOM_TRANSLATE_USE_DATABASE=true
DOM_TRANSLATE_LOG_LEVEL=3
DOM_TRANSLATE_LOG_INDICATOR=dom-translate
DOM_TRANSLATE_PROVIDER=google
DOM_TRANSLATE_GOOGLE_KEY=
DOM_TRANSLATE_BING_KEY=
DOM_TRANSLATE_HASH_SALT=DzBQ2DxKhNaF
DOM_TRANSLATE_HASH_ALGO=sha256
DOM_TRANSLATE_LANG_SRC=en
DOM_TRANSLATE_LANG_DEST=af
DOM_TRANSLATE_ROUTES_ENABLED=true
DOM_TRANSLATE_ROUTES_PREFIX=api/translate
DOM_TRANSLATE_ROUTES_MIDDLEWARE=web
DOM_TRANSLATE_SESSION_KEY=app_language_code
```

-   If `DOM_TRANSLATE_USE_SESSION` is `true`, translations will be saved in the session and used as the first point of retrieval.
-   If no translations are found in the session, or if `DOM_TRANSLATE_USE_SESSION` is `false`, translations will be retrieved from the database, provided they have been previously stored there.
-   If translations are still not found, or if both `DOM_TRANSLATE_USE_SESSION` and `DOM_TRANSLATE_USE_DATABASE` are `false`, translations will be sourced from a third-party translation service (e.g., Google Translate).
-   Depending on whether `DOM_TRANSLATE_USE_SESSION` and `DOM_TRANSLATE_USE_DATABASE` are `true`, the retrieved translation will be saved to either the session or the database.
-   We strongly recommend setting `DOM_TRANSLATE_USE_DATABASE` to `true` _(default is `true` if not specified in your .env)_ to ensure we don't make repeated API calls _(also it's slower calling the API verses db/session lookup)_.

> **Note:** If you don't have a [Google Cloud Platform](https://cloud.google.com/gcp) account, sign up and create a new project. Add the _Cloud Translation API_ to it. You can use [Insomnia](https://insomnia.rest/download) to test your API key.

<a href="https://ibb.co/R0dwJ78" target="_blank"> <img src="https://i.ibb.co/wWjm2Yt/insomnia.png" alt="insomnia" border="0" width="100%" /> </a>

### 📋 Step-by-Step Guide: Getting Your Google Translate API Key

Follow these detailed steps to obtain your Google Translate API key:

#### Step 1: Create a Google Cloud Account & Project
1. **Visit Google Cloud Console**: Go to [https://console.cloud.google.com/](https://console.cloud.google.com/)
2. **Sign In/Register**: Sign in with your Google account or create a new one
3. **Accept Terms**: Accept the Google Cloud Platform Terms of Service
4. **Create New Project**:
   - Click on the project dropdown (top-left, next to "Google Cloud Platform")
   - Click "New Project"
   - Enter a project name (e.g., "My Translation App")
   - Click "Create" and wait for the project to be created
   - **Select your new project** from the project dropdown

#### Step 2: Enable Billing (Required)
1. **Go to Billing**: In the left sidebar, click "Billing" or visit [https://console.cloud.google.com/billing](https://console.cloud.google.com/billing)
2. **Link Billing Account**:
   - If you don't have a billing account, click "Create Account" and follow the setup
   - Add a valid credit card (Google offers $300 free credits for new accounts)
   - Link the billing account to your project

#### Step 3: Enable the Cloud Translation API
1. **Go to APIs & Services**: In the left sidebar, click "APIs & Services" > "Library"
2. **Search for Translation API**: Search for "Cloud Translation API"
3. **Enable the API**:
   - Click on "Cloud Translation API"
   - Click the "Enable" button
   - Wait for the API to be enabled (may take a few moments)

#### Step 4: Create API Credentials
1. **Go to Credentials**: In the left sidebar, click "APIs & Services" > "Credentials"
2. **Create API Key**:
   - Click "Create Credentials" dropdown
   - Select "API key"
   - Your new API key will be generated and displayed
   - **Copy the API key** and store it securely

#### Step 5: Restrict Your API Key (Recommended for Security)
1. **Edit API Key**: Click on the API key you just created to edit it
2. **Add API Restrictions**:
   - In the "API restrictions" section, select "Restrict key"
   - Check "Cloud Translation API" from the list
   - Click "Save"
3. **Add Application Restrictions** (Optional but recommended):
   - Choose "HTTP referrers" for web applications
   - Add your domain(s), e.g., `*.yourdomain.com/*`
   - Or choose "IP addresses" for server applications
   - Click "Save"

#### Step 6: Add API Key to Your Laravel Project
1. **Add to .env file**: Copy your API key to your Laravel `.env` file:
   ```env
   DOM_TRANSLATE_GOOGLE_KEY=AIzaSyDaGmWKa4JsXZ-HjGw7ISLn_3namBGewQe
   ```
2. **Clear config cache**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

#### Step 7: Test Your Setup
You can test your API key using a simple curl command:
```bash
curl -X POST \
  -H "Content-Type: application/json; charset=utf-8" \
  -d @- \
  "https://www.googleapis.com/language/translate/v2?key=YOUR_API_KEY" <<EOF
{
  'q': 'Hello world',
  'target': 'fr'
}
EOF
```

#### 💡 Important Notes:
- **Free Tier**: Google offers 500,000 characters per month free
- **Billing Required**: Even for free tier usage, you must have a valid billing account
- **Security**: Always restrict your API keys to prevent unauthorized usage
- **Rate Limits**: Be aware of quota limits and implement proper error handling
- **Cost Monitoring**: Set up billing alerts to monitor your usage

#### 🔧 Troubleshooting:
- **403 Forbidden**: Check if the Cloud Translation API is enabled for your project
- **Invalid API Key**: Ensure the key is copied correctly and has proper restrictions
- **Billing Issues**: Verify that billing is enabled and your account is in good standing
- **Rate Limits**: Implement exponential backoff for handling rate limit errors

Review any configuration file changes that you might want to make. The config file is published to the main config folder.

> You're all set! 😉

Restart your service and update your Blade files with the `@transl8` directive. Only new untranslated phrases will trigger an API call. Future requests for the same phrase will be retrieved from the database.

## HTML Blade Example

Here are a few examples of how to use the translate Blade directive in your HTML (Blade) files:

```blade
<div>
    {{-- Default usage: Only provide a phrase --}}
    <p>@transl8("I like this feature.")</p>

    {{-- Specify a destination language --}}
    <p>@transl8("We need to test it in the staging environment.", "de")</p>

    {{-- Specify both source and destination languages --}}
    <p>@transl8("Wie weet waar Willem Wouter woon?", "en", "af")</p>

    {{-- Language-specific directives --}}
    <p>@transl8fr("This phrase will be translated to French.")</p>
    <p>@transl8de("This phrase will be translated to German.")</p>
    <p>@transl8je("This phrase will be translated to Japanese.")</p>

    {{-- A phrase that will not be translated --}}
    <p>This phrase will not be translated.</p>

    {{-- Use the below method if you want to use a session or cookie to set the destination language. --}}
    {{-- Example: though a drop-down menu --}}
    <p>@transl8auto("This phrase will auto-translated to whatever the session/cookie define language might be.")</p>
    {{-- ☝️ has a bit more configuration to do (refer to documentation below) but works best... --}}
    {{-- ...if you want to make it easy for people to choose their language once --}}
</div>
```

## Blade Directive Example

Four directives are available by default (`@transl8()` is the main one). You can add more in your Laravel _AppServiceProvider_ file (under the `register()` method).

```php
    // ---------------
    // Register the default Blade directive - @transl8()
    // Only the phrase argument is required. Default source and destination languages are sourced from the config file.
    // - Format: transl8('Phrase','target','source')
    // - Example: transl8('This must be translated to French.','fr')
    // Register the @transl8 directive separately
    Blade::directive('transl8', function ($string) {
        return "<?= app(" . TranslateController::class . "::class)->phrase({$string}); ?>";
    });

    // ---------------
    // Register an auto translation directive that will use a session or cookie to determine the destination language
    Blade::directive('transl8auto', function ($string) {
        return TranslateHelper::autoTransl8(
            $string, // what we want to translate
            TranslateHelper::currentDefinedLanguageCode() // this will use the current user's language preference (saved in session or cookie)
        );
    });

    // ---------------
    // Register the @transl8 directive for specific languages you use often
    $languages = [
        'fr', // French
        'de', // German
        'nl', // Dutch
        'es', // Spanish
        'it', // Italian
        'pt', // Portuguese
        'ru', // Russian
        'zhcn' => 'zh-CN', // Chinese Simplified
        'zhtw' => 'zh-TW', // Chinese Traditional
        'af', // Afrikaans
        'ar' => 'ar-SA', // Arabic
        // ... Add more languages as needed
    ];

    // Register directives for each language by iterating over the languages array
    foreach ($languages as $alias => $langCode) {
        // Handle array values like 'zhcn' => 'zh-CN'
        $directive = is_string($alias) ? $alias : $langCode;
        Blade::directive("transl8{$directive}", function ($string) use ($langCode) {
            return "<?= app(" . TranslateController::class . "::class)->translate({$string}, '{$langCode}', 'en'); ?>";
        });
    }
```

## API Routes for Language Management

The package automatically provides HTTP endpoints for setting and getting the user's preferred language. These routes are enabled by default but can be configured or disabled via the config file.

### Available Endpoints

**Set Language Preference**
```http
POST /api/translate/set-language
Content-Type: application/json

{
    "language": "fr"
}
```

**Get Current Language Preference**
```http
GET /api/translate/get-language
```

### JavaScript Example

```javascript
// Set user's preferred language
async function setLanguage(langCode) {
    const response = await fetch('/api/translate/set-language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ language: langCode })
    });

    const result = await response.json();
    console.log(result.message); // "Language preference set successfully."
}

// Get current language preference
async function getCurrentLanguage() {
    const response = await fetch('/api/translate/get-language');
    const result = await response.json();
    console.log(result.language); // e.g., "fr"
}
```

### Configuration Options

Add these optional environment variables to customize the routes:

```env
DOM_TRANSLATE_ROUTES_ENABLED=true           # Enable/disable routes (default: true)
DOM_TRANSLATE_ROUTES_PREFIX=api/translate   # Route prefix (default: api/translate)
DOM_TRANSLATE_ROUTES_MIDDLEWARE=web         # Middleware (default: web)
DOM_TRANSLATE_SESSION_KEY=app_language_code # Session/cookie key (default: app_language_code)
```

### Custom Implementation

If you prefer to implement your own routes and controller, you can disable the automatic routes by setting `DOM_TRANSLATE_ROUTES_ENABLED=false` in your `.env` file and use the helper methods directly:

```php
use Wazza\DomTranslate\Helpers\TranslateHelper;

class YourLanguageController extends Controller
{
    public function setLanguage(Request $request)
    {
        $langCode = $request->input('language');
        return TranslateHelper::setLanguage($langCode);
    }

    public function getLanguage()
    {
        return TranslateHelper::getLanguage();
    }
}
```

## Auto-Translation with User Language Preferences (`@transl8auto`)

The `@transl8auto()` directive is the most powerful feature of this package. Unlike the basic `@transl8()` directive that requires you to specify the target language, the auto directive automatically translates content based on the user's preferred language stored in their session or cookie.

### How Auto-Translation Works

The auto-translation system follows this priority order:

1. **Session**: Checks for the user's language preference in the session
2. **Cookie**: Falls back to a long-term cookie if no session is found
3. **Config Default**: Uses the default destination language from your config file
4. **App Locale**: Falls back to Laravel's app locale
5. **English**: Final fallback to English if nothing else is set

### Basic Usage

```blade
{{-- Auto-translate based on user's language preference --}}
<h1>@transl8auto("Welcome to our website!")</h1>
<p>@transl8auto("This content will be translated to the user's preferred language.")</p>

{{-- Mix with regular content --}}
<div>
    <h2>@transl8auto("About Us")</h2>
    <p>@transl8auto("We are a global company serving customers worldwide.")</p>
    <button>@transl8auto("Contact Us")</button>
</div>
```

### Complete Implementation Guide

To implement a fully functional language selector with auto-translation, follow these steps:

#### Step 1: Create a Language Selector Dropdown

Add this language selector to your navigation or header layout:

```blade
{{-- resources/views/layouts/app.blade.php --}}
<div class="language-selector">
    <select id="languageSelector" class="form-select">
        <option value="en">🇺🇸 English</option>
        <option value="es">🇪🇸 Español</option>
        <option value="fr">🇫🇷 Français</option>
        <option value="de">🇩🇪 Deutsch</option>
        <option value="it">🇮🇹 Italiano</option>
        <option value="pt">🇵🇹 Português</option>
        <option value="nl">🇳🇱 Nederlands</option>
        <option value="ru">🇷🇺 Русский</option>
        <option value="zh">🇨🇳 中文</option>
        <option value="ja">🇯🇵 日本語</option>
        <option value="ar">🇸🇦 العربية</option>
    </select>
</div>
```

#### Step 2: Add JavaScript for Language Selection

Add this JavaScript to handle language switching:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const languageSelector = document.getElementById('languageSelector');

    // Load current language preference on page load
    loadCurrentLanguage();

    // Handle language change
    languageSelector.addEventListener('change', function() {
        const selectedLanguage = this.value;
        setLanguage(selectedLanguage);
    });

    // Function to set language preference
    async function setLanguage(langCode) {
        try {
            const response = await fetch('/api/translate/set-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ language: langCode })
            });

            const result = await response.json();
            if (result.message) {
                console.log('Language set to:', result.language);

                // Show success message (optional)
                showNotification('Language preference updated!', 'success');

                // Reload page to see translations
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        } catch (error) {
            console.error('Error setting language:', error);
            showNotification('Failed to update language preference', 'error');
        }
    }

    // Function to load current language preference
    async function loadCurrentLanguage() {
        try {
            const response = await fetch('/api/translate/get-language');
            const result = await response.json();

            if (result.language) {
                languageSelector.value = result.language;
            }
        } catch (error) {
            console.error('Error loading current language:', error);
        }
    }

    // Optional: Show notification function
    function showNotification(message, type = 'info') {
        // Implement your preferred notification system
        // Example with a simple alert (replace with your notification library)
        if (type === 'success') {
            console.log('✅ ' + message);
        } else {
            console.log('❌ ' + message);
        }
    }
});
```

#### Step 3: Add CSS Styling (Optional)

Style your language selector:

```css
.language-selector {
    position: relative;
    display: inline-block;
}

.language-selector select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    font-size: 14px;
    min-width: 150px;
}

.language-selector select:hover {
    border-color: #007bff;
}

.language-selector select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}
```

#### Step 4: Update Your Views with Auto-Translation

Replace your static text with auto-translation directives:

```blade
{{-- Before --}}
<h1>Welcome to Our Website</h1>
<p>We provide excellent services worldwide.</p>
<button>Get Started</button>

{{-- After --}}
<h1>@transl8auto("Welcome to Our Website")</h1>
<p>@transl8auto("We provide excellent services worldwide.")</p>
<button>@transl8auto("Get Started")</button>
```

### Advanced Configuration

#### Custom Session/Cookie Key

If you want to use a different session/cookie key name:

```env
# .env file
DOM_TRANSLATE_SESSION_KEY=my_custom_language_key
```

#### Disable Routes (Use Custom Implementation)

If you prefer to create your own language switching endpoints:

```env
# .env file
DOM_TRANSLATE_ROUTES_ENABLED=false
```

Then create your own controller:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Wazza\DomTranslate\Helpers\TranslateHelper;

class LanguageController extends Controller
{
    public function setLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|string|min:2|max:5'
        ]);

        return TranslateHelper::setLanguage($request->input('language'));
    }

    public function getLanguage()
    {
        return TranslateHelper::getLanguage();
    }
}
```

### Best Practices

1. **Fallback Strategy**: Always ensure English content is available as a fallback
2. **Performance**: The first translation of each phrase will hit the Google API, subsequent requests use the database
3. **User Experience**: Consider showing a loading indicator during language changes
4. **SEO**: For SEO-sensitive applications, consider implementing hreflang tags
5. **Testing**: Test with different language combinations to ensure proper fallbacks

### Troubleshooting Auto-Translation

**Problem**: Auto-translation not working
- **Solution**: Check if the session/cookie is being set correctly
- **Debug**: Use browser dev tools to inspect the network requests

**Problem**: Translations not persisting between sessions
- **Solution**: Ensure cookies are being set with proper domain and path settings

**Problem**: Some phrases not translating
- **Solution**: Check your Google API quotas and error logs

### Example: Complete Language Switcher Component

Here's a complete Vue.js component for language switching:

```vue
<template>
    <div class="language-switcher">
        <select v-model="currentLanguage" @change="changeLanguage" class="form-select">
            <option v-for="lang in languages" :key="lang.code" :value="lang.code">
                {{ lang.flag }} {{ lang.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    data() {
        return {
            currentLanguage: 'en',
            languages: [
                { code: 'en', name: 'English', flag: '🇺🇸' },
                { code: 'es', name: 'Español', flag: '🇪🇸' },
                { code: 'fr', name: 'Français', flag: '🇫🇷' },
                { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
                // Add more languages as needed
            ]
        }
    },

    async mounted() {
        await this.loadCurrentLanguage();
    },

    methods: {
        async changeLanguage() {
            try {
                const response = await fetch('/api/translate/set-language', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ language: this.currentLanguage })
                });

                const result = await response.json();
                if (result.message) {
                    this.$toast.success('Language updated!');
                    setTimeout(() => window.location.reload(), 500);
                }
            } catch (error) {
                this.$toast.error('Failed to update language');
            }
        },

        async loadCurrentLanguage() {
            try {
                const response = await fetch('/api/translate/get-language');
                const result = await response.json();
                this.currentLanguage = result.language || 'en';
            } catch (error) {
                console.error('Error loading language:', error);
            }
        }
    }
}
</script>
```

## Future Development (Backlog)

-   Translations are not always perfect. Create a Phrase vs Translation admin section that will allow admin users to change (update) a translated phase with corrections.
-   Create alternative translation engines. Currently, only Google Translate is supported via `Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate()`. Other options to consider include NLP Translation, Microsoft Translator, etc.

```php
// Line 14 in 'wazza\dom-translate\config\dom_translate.php'
// Third-party translation service providers
'api' => [
    'provider' => env('DOM_TRANSLATE_PROVIDER', 'google'),
    'google' => [
        'controller' => "Wazza\DomTranslate\Controllers\ApiTranslate\GoogleTranslate",
        'endpoint' => "https://www.googleapis.com/language/translate/v2",
        'action' => "POST",
        'key' => env('DOM_TRANSLATE_GOOGLE_KEY', null), // https://console.cloud.google.com/apis/credentials
    ],
    // To contribute, fork the project and add more translation providers here, implementing CloudTranslateInterface
],
```

## Running Local Tests

Run the following command to execute tests:

```bash
.\vendor\bin\phpunit
```

**Important:** For the final two assert tests to work, add your personal [Google Translate key](https://console.cloud.google.com/apis/credentials) as `DOM_TRANSLATE_GOOGLE_KEY=xxx` in your `.env` file (free options are available at the time of writing), or directly in the `phpunit.xml` file under `DOM_TRANSLATE_GOOGLE_KEY`.
