# Release Notes
## v2.0.0 - 2023-10-26
### Added
- **Support for Laravel v10.x:** Updated domTranslate to Laravel.v10 support. (Previous versions v1.x supported Laravel v7, and v2.x supports Laravel v10.)
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
