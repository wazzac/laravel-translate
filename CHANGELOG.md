# Release Notes

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
