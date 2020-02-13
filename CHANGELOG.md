# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and starting with version v0.1.0, this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- response()->vuex() and response()->phase() now behave the same. `->phase()` is preferred.
- `Vuex::lazyLoad($namespace, $key)` has been introduced so now ModuleLoaders can easily be lazy loaded without modification
- `Vuex::commit($mutation, $value)` has been added
- `Vuex::dispatch($action, $value)` has been added
- Added actions/mutations to axios interceptors

### Deprecated
- `response()->vuex()` is deprecated, and response()->phase() is the preferred, however there are currently no plans to remove it.
- `Vuex::store(...)` is deprecated however there are no current plans for removal

### Fixed
- @phased/laravel-mix plugin should better track changes to `routes/web.php`
- config('phase') should pick up a project `config/phase.php` overrides again
- phpunit phased/routing tests were fixed
- `->toVuex()` without only one argument again saves to base state i.e. `->toVuex('user')` => `this.$store.state.user`
