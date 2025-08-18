# Release Notes

## v2.4.1 `2025-08-18`

### Fixed
- Fixed a bug where the `./src/Helpers/PhraseHelper.php` file was incorrectly named (started with a small p).

## v2.4.0 `2025-07-27`

### Added
- **Auto-Translation API Routes:** Added automatic HTTP endpoints (`/set-language/{code}` and `/get-language`) for seamless language management via AJAX/JavaScript
- **SetLocaleMiddleware:** New middleware that automatically synchronizes Laravel's application locale with the translation system and Carbon date localization
- **Automatic View Cache Clearing:** Language changes now automatically clear Laravel's view cache to ensure immediate translation updates
- **Enhanced TranslateHelper:** Added `currentDefinedLanguageCode()` method and improved `setLanguage()` with automatic cache clearing
- **LanguageController:** New dedicated controller for handling language preference API endpoints with proper validation and JSON responses
- **Comprehensive Documentation:** Extensively enhanced README.md with step-by-step Google Cloud Translation API setup, JavaScript examples, Vue.js components, troubleshooting guides, and best practices

### Changed
- **Unified Language Experience:** The package now provides seamless integration between custom translations and Laravel's built-in localization system
- **Improved Performance:** Automatic cache management ensures optimal performance without manual intervention
- **Enhanced Developer Experience:** Complete documentation with real-world examples, troubleshooting guides, and implementation patterns

### Fixed
- **View Cache Persistence:** Resolved issues where translated content would persist in view cache after language changes
- **Locale Synchronization:** Fixed discrepancies between custom translation system and Laravel's locale settings


## v2.3.0 `2025-06-17`

### Changed
- Refactored `TranslateController` to use instance (non-static) methods and registered it as a singleton in the service provider.
- Updated all Blade directives in `DomTranslateServiceProvider` to use the singleton instance via `app(TranslateController::class)`.
- Improved Blade directive registration for proper PHP syntax and singleton usage.


## v2.2.1 `2024-07-21`
### Updated
- Renamed the `PhraseHelper.php` class to conform to PSR-1.

## v2.2.0 `2024-07-15`
### Added
- Added `DOM_TRANSLATE_USE_DATABASE` to the config file. This will allow you enable or disable the db phrase and translation storage.

### Updated
- Updated the Readme.md documentation and made a few improvements.
- General improvements to package logging.

## v2.1.0 `2024-06-25`
### Added
- `CODE_OF_CONDUCT.md` file.
- `CONTRIBUTING.md` file.
- `SECURITY.md` file.

### Updated
- Composer packages.

## v2.0.0 `2023-10-26`
### Added
- **Support for Laravel v10.x:** Updated domTranslate to Laravel.v10 support. _(Previous versions v1.x supported Laravel v7, and v2.x supports Laravel v10.)_
- **Additional Blade Directives:** Included more language-specific Blade directives for enhanced translation capabilities. New supported options include:
    ```blade
    {{-- Original --}}
    <p>@transl8fr("This phrase will be translated to French.")</p>
    <p>@transl8de("This phrase will be translated to German.")</p>
    <p>@transl8je("This phrase will be translated to Japanese.")</p>
    {{-- New --}}
    <p>@transl8nl("This phrase will be translated to Dutch.")</p>
    <p>@transl8es("This phrase will be translated to Spanish.")</p>
    <p>@transl8it("This phrase will be translated to Italian.")</p>
    <p>@transl8pt("This phrase will be translated to Portuguese.")</p>
    <p>@transl8ru("This phrase will be translated to Russian.")</p>
    <p>@transl8zhcn("This phrase will be translated to Chinese Simplefied.")</p>
    <p>@transl8zhtw("This phrase will be translated to Chinese Traditional.")</p>
    <p>@transl8af("This phrase will be translated to Afrikaans.")</p>
    <p>@transl8ar("This phrase will be translated to Arabic.")</p>
    ```
- **Improved Documentation:** Enriched the documentation for easier integration and usage.
- **Enhanced Testing:** Updated testing to ensure compatibility and proper functionality. All tests succeeded.
