# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
As of version v0.1.0, this project as a whole adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html), however individual components may get bumped in order to maintain version sync with their peers.

## [Planned]
- add `--model=Users\User --keys=all,active` options to `artisan make:loader` command for increased stub generation usability.
- add `files: []` watch option for webpack plugin

## [Unreleased]

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


## [0.1.0] - 2020-02-13

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
