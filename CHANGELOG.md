# Release Notes

## v2.0.0 - 2023-10-26

- Updated domTranslate to [Laravel.v10](https://github.com/laravel/laravel/tree/10.x) support. *(v1.x support Laravel.v7; v2.x Laravel.v10)*
- Added more language specific Blade directives. Below are the supported options:
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
- Improved documentation.
