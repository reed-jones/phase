# Phase

## Installation
  - `npm i -D @phased/phase` (meta package)
  - `composer require phased/routing`
  - `composer require phased/state`

  - * Note Currently routing depends on state being installed, but further decoupling is planned so that the two packages may be used independently. State however is standalone at this point and can be used by itself if no SPA routing is required. just install `@phased/state` instead of the `@phased/phase` meta package.

## Setup
  - For frontend vuex integration, import the `hydrate` helper `new Store(hydrate({ ...yourState }))`.
  - For Vue Router, dynamically import routes from `@phased` and use that for your route definitions

## Routing
  - Web Routes
    - `routes/web.php` -> `Route::phase('/url/to/page', 'YourController@method');`
    - -> in your controller, `return Phase::view();`
    - Now benefit from preloaded vuex data on page initial page load, and automatic updated vuex store on page changes!
    - For pure API calls that just want to update vuex store automatically you can use `return response()->vuex()` with any additional data passed as if it was `->json`, `->vuex($extraData)`
  - Folder structure...

## State Management
  - Api
    - `Vuex::module($namespace, $state);`
    - `Vuex::state($state);`
  - Collections
    - `->toVuex($namespace, $key)`
  - Models
    - Vuexable Trait
    - `->toVuex($namespace, $key)` (same as collections)
  - Lazy Loading
  - Module Loaders/Module Definitions
    - `Vuex::register([ UserModuleDefinition::class ]);` Put this into AppServiceProvider
    - `Vuex::load($namespace, $key);` will load the data from the module definition above. If not specified, the namespace will attempt to be pulled from the class name, but can be overridden using `protected $namespace = 'users'` for example. The `$key` will both be the where in the front end the data will be saved, and the name of the function on the class to be called.

## Troubleshooting
  - `dd(Vuex::toArray());` will dump all the currently saved vuex state
  - For Api calls, the mutation will be visible from within the Vue DevTools mutations tab
