# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
As of version v1.0.0, this project as a whole will adhere to [Semantic Versioning](https://semver.org/spec/v2.0.0.html), however individual components may get bumped in order to maintain version sync with their peers. Until v1.0.0 is reached, minor version (0.X.0) updates may include breaking changes, however this will be avoided where possible and clearly documented if it occurs.

## [Planned]
- add `--model=Users\User --keys=all,active` options to `artisan make:loader` command for increased stub generation usability.
- add `files: []` watch option for webpack plugin
- lazily evaluated actions
- lazily evaluated mutations
-

## [Unreleased](https://github.com/reed-jones/phase/compare/v0.5.0...master)

## Added
- Added Dependency Injection for `Vuex::load` & `Vuex::lazyLoad`

## Changed

## Deprecated

## Removed

## Fixed

## Security

## [v0.5.0](https://github.com/reed-jones/phase/compare/v0.4.0...v0.5.0) - 2020-08-03

## Added
- added `Phased/Routing/Commands/GeneratePhaseRouter::getJsonOutput($withConfig)` helper to encode & retrieve output
- added `phase.assets.ssr.server` & `phase.assets.ssr.client` configuration for js bundles.
- added `Vuex::get`
- Front end log levels. passing `{ logLevel: 'debug' }` as the options to the hydrate method, for example will enable the most verbose logging.
- 'example' app with cypress/pest test suite, tailwindcss, laravel auth
- Added gzipped filesize output to buildscript
- removed default stylesheet `sass/app.scss` from config
- helper functions added to example app such as 'resolve route by phase name' (phaseRoute `example/app/helpers.php`)
- added `vue-ssr-failed` attribute to blade file when an error occurs during rendering
- [experimental] added `Phased/State/Factories/VuexFactory::get(...)` method to access previously saved state.
- Migrated test suite to use PestPHP & Cypress (for browser based tests)

## Changed
- Migrated test suite to use `pest`
- `Phased/Routing/Commands/GeneratePhaseRouter::getFormattedRoutes()` changed from protected to public
- `Phased/Routing/Commands/GeneratePhaseRouter::outputTable()` changed from protected to public
- `Phased/Routing/Commands/GeneratePhaseRouter::outputJson()` changed from protected to public
- `Phased/Routing/Factories/PhaseFactory::addRoute()` signature changed from variadic `(...$args)` to `($uri, $action)`

## Deprecated
- Automatic axios redirects. This may be revived in a future release.

## Removed
- auto axios redirects (301/302) have been removed. For direct entry redirects (301/302) the standard laravel redirects can be used. For SPA redirects, these must by manually set using vue-router.

## Fixed

## Security


## [v0.4.0](https://github.com/reed-jones/phase/compare/v0.3.0...v0.4.0) - 2020-03-15
### Added
- Now follows any axios redirects (with page transition, enabled by default)
- Customizable `<head>` section (meta tags, etc) using optional `parts/head.blade.php`
- Route code splitting now available using the option `codeSplit: true` in `webpack.mix.js`

### Changed
- All automated ajax requests append `phase=true` to the query string.

### Fixed
- After using vue-router, then navigating to an external site, pressing 'back' no longer displays json

## [v0.3.0](https://github.com/reed-jones/phase/compare/v0.2.0...v0.3.0) - 2020-03-07
### Added
- Server Side Rendering option available in `config('phase.ssr')` (true/false)
- Client Hydration via `config('phase.hydrate')` (SSR & no JS bundle)
- `NODE_PATH=` env variable has been added and is required for SSR support to operate
### Changed
- ** Breaking ** main vue app should now `export default new Vue` and not mount the app (no `el: '#app'`). This allows for SSR to be toggled on/off.
- ** Breaking ** It is now mandatory & non-configurable that the main entry is `app.js`.
### Removed
- ** Breaking ** `js` option in assets configuration is no longer used since SSR option has been added, and has been removed. If your js bundle was named something other than `app.js` this is a breaking change.
### Fixed
- `@phased/state` no longer relies on `window` making it usable for other environments (primarily SSR, potentially NativeScript-vue)


## [v0.2.0](https://github.com/reed-jones/phase/compare/v0.1.0...v0.2.0) - 2020-03-01

### Added
- webpack watch mode now watches for changes to `routes/web.php`
- `php artisan make:loader {module}` Generate empty Module Loader stubs
- `Phased\State\Factories\VuexFactory::dd()` dd's the current store data
- `Phased\State\Middleware\ModuleLoaderMiddleware::class` module loading middleware. Can now load common vuex state for groups or routes. `->middleware('load:authors,all|posts,all|books,active,1)`

### Changed
- ** Breaking ** moved PhaseRoutes import from `@phased/webpack-plugin/routes` to `@phased/phase/routes` when using the `@phased/phase` meta package. If using the webpack-plugin directly, there is no change.
- Changed private methods to protected in `Phased\State\Factories\VuexFactory`

### Fixed
- laravel-mix plugin no longer requires an empty object passed as a minimum options configuration... (`.phase()` works again)


## [0.1.0](https://github.com/reed-jones/phase/compare/v0.0.3...v0.1.0) - 2020-02-13

### Added
- `response()->vuex()` and `response()->phase()` now behave the same. `response()->phase()` is preferred.
- `Phased\State\Factories\VuexFactory::lazyLoad($namespace, $key)` has been introduced so now ModuleLoaders can easily be lazy loaded without modification
- `Phased\State\Factories\VuexFactory::commit($mutation, $value)` has been added
- `Phased\State\Factories\VuexFactory::dispatch($action, $value)` has been added
- Added automatic actions/mutations to axios interceptors

### Deprecated
- `response()->vuex()` is deprecated, and `response()->phase()` is the preferred, however there are currently no plans to remove it.
- `Phased\State\Factories\VuexFactory::store(...)` is deprecated however there are no current plans for removal

### Fixed
- @phased/laravel-mix plugin should better track updates to `routes/web.php` during npm run hot/watch
- config('phase') should pick up a project `config/phase.php` overrides again
- phpunit phased/routing tests were fixed
- `->toVuex()` with only one argument again saves to base state i.e. `->toVuex('user')` => `$store.state.user`
