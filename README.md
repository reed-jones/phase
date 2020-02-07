# Phase

## Why
Phase aims to integrate Laravel, Vuex, & Vue Router as seamlessly as possible. All phase routes specified in your `routes/web.php` are automatically configured for slick SPA navigation. All configured api calls will automatically be committed into your vuex store. Data loaded through your view controllers is immediately available in the vuex store. No waiting for separate api calls, No `mutation` boilerplate: `state.count = count`. No chance of your vue-router configuration getting out of sync with your web routes. No reason to give up the nice Route -> Controller -> Page view flow.

## Installation
  - `npm i -D @phased/phase` (meta package)
  - `composer require phased/routing`
  - `composer require phased/state`

  - * Note Currently routing depends on state being installed, but further decoupling is planned so that the two packages may be used independently. State however is standalone at this point and can be used by itself if no SPA routing is required. just install `@phased/state` instead of the `@phased/phase` meta package.

## Client Setup (Front End)

Both state & routing rely on `axios` being globally available, in order to automatically configure the interceptors required. You may do this however you wish, but the standard lines that come with Laravel work just fine.
```js
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
```

### Vuex Integration/State Management
Follow the official Vuex installation/setup. When you create your store, wrap your options using the @phased/state `hydrate` method. Adding onto the Vuex [Simplest Store](https://vuex.vuejs.org/guide/#the-simplest-store) example:
```js
// store.js
import Vue from 'vue'
import Vuex, { Store } from 'vuex'
import { hydrate } from '@phased/state'

Vue.use(Vuex)

export default new Store(hydrate({
  state: {
    count: 0
  },
  mutations: {
    increment (state) {
      state.count++
    }
  }
}))
```

### Vue Router/Route Management
Front end Vue Router integration falls into two steps. Configuring Laravel Mix (or Webpack), and setting up the router. Router setup is regular vue-router setup, so feel free to refer to the [docs.](https://router.vuejs.org/guide/). Phased however makes it much simpler as it supplies the route definitions. Below is a simple, yet complete example of the router configuration.
```js
// router.js
import Vue from 'vue'
import VueRouter from 'vue-router';
import PhaseRoutes from '@phased/webpack-plugin/routes'

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: PhaseRoutes
})
```

Finally if using Laravel-mix add
```js
// webpack.mix.js
require('@phased/phase')
mix.phase()
```

Phase configuration pulls the required assets (js/scss files) from the phase config,
A slightly more complex/realistic configuration with tailwind setup would look along the lines of
```js
// webpack.mix.js
const mix = require('laravel-mix')
const path = require('path')
const tailwindcss = require('tailwindcss')
require('@phased/phase')

mix
    .webpackConfig({
        resolve: { alias: { "@": path.resolve(__dirname, 'resources', 'js') } },
    })
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .phase()
```
## Server Setup (Back End)

> Coming Soon

## Usage

> Coming Soon

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
