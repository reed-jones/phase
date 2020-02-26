# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
As of version v0.1.0, this project as a whole adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html), however individual components may get bumped in order to maintain version sync with their peers.

## [Planned]
- add `--model=Users\User --keys=all,active` options to `artisan make:loader` command.
- laravel-mix watch/hot to recompile on changes to `routes/web.php`

## [Unreleased]
- `php artisan make:loader` Generate empty Module Loader stubs
- `Phased\State\Facades\Vuex::dd()` dd's the current store data
- `Phased\State\Middleware\ModuleLoaderMiddleware::class` module loading middleware
```php
Route::middleware(['auth', 'load:users,active|posts,all'])->group(function () {
    Route::phase('profile', "PhaseController@ProfilePage");
});
```

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
- @phased/laravel-mix plugin should better track changes to `routes/web.php`
- config('phase') should pick up a project `config/phase.php` overrides again
- phpunit phased/routing tests were fixed
- `->toVuex()` with only one argument again saves to base state i.e. `->toVuex('user')` => `$store.state.user`
