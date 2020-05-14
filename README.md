# [Phase](https://phased.dev)

## [Demo](https://github.com/reed-jones/phase-blog-demo)
Check out the demo. Deploy a fully configured Phase app in minutes, poke around, change things, view source, have fun!

---

This following README can be a little rough around the edges, and is meant more as a quick reference than a guide. There is an ongoing effort to improve the documentation found at [phased.dev](https://phased.dev), which is where you should start if you are new to phase. If your having trouble setting Phase up, or if something isn't working as expected, feel free to [open an issue](https://github.com/reed-jones/phase/issues/new).

---

Contributions welcome! contributions come in all forms, bug reports, questions asked, questions answered, documentation, and of course writing code. If your interested, but not sure where to start, open an issue.

---

## Why
Phase aims to integrate Laravel, Vuex, & Vue Router as seamlessly as possible. All phase routes specified in your `routes/web.php` are automatically configured for slick SPA navigation. All configured api calls will automatically be committed into your vuex store. Data loaded through your view controllers is immediately available in the vuex store. No waiting for separate api calls, No `mutation` boilerplate: `state.count = count`. No chance of your vue-router configuration getting out of sync with your web routes. No reason to give up the nice Route -> Controller -> Page view flow.

## Installation
  - `npm install --save-dev @phased/phase`
  - `composer require phased/routing`
  - `composer require phased/state`

  - * Note Currently routing depends on state being installed, but further decoupling is planned so that the two packages may be used independently. State however is standalone at this point and can be used by itself if no SPA routing is required. For this configuration only `npm install @phased/state` & `composer require phased/state` are needed.

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
Front end Vue Router integration falls into two steps. Configuring Laravel Mix (or Webpack), and setting up the router. Router setup is regular vue-router setup, so feel free to refer to the [docs.](https://router.vuejs.org/guide/). Phased however makes it much simpler as it supplies the route definitions. Below is a simple, yet complete example of the router configuration. For many use cases, this is all that will be required.
```js
// router.js
import Vue from 'vue'
import VueRouter from 'vue-router';
import PhaseRoutes from '@phased/phase/routes'

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: PhaseRoutes
})
```

Finally if using Laravel-mix add
```js
// webpack.mix.js
const mix = require('laravel-mix')
require('@phased/phase')
mix.phase()
```

Alternatively the webpack plugin is exposed, and can be used directly
```js
const VueRouterAutoloadPlugin = require("@phased/webpack-plugin")
//...
  plugins: [
    new VueRouterAutoloadPlugin({})
  ]
```

Phase configuration pulls the required assets (js/scss files) from the phase config,
A slightly more complex/realistic configuration with tailwind setup would look along the lines of
```js
// webpack.mix.js
const mix = require('laravel-mix')
const path = require('path')
const tailwindcss = require('tailwindcss')
require('laravel-mix-purgecss');
require('@phased/phase')

mix
    .webpackConfig({
        resolve: { alias: { "@": path.resolve(__dirname, 'resources', 'js') } },
    })
    .options({
        processCssUrls: false,
        postCss: [ tailwindcss('./tailwind.config.js') ],
    })
    .purgeCss()
    .phase()
```

## Server Setup (Back End)

After installing both routing & state components with composer, Phase is ready to roll. No really. Thats all the required setup. For more customization options, a 'phased' config is exposed and can be published. `php artisan vendor:publish --provider="Phased\Routing\PhasedRoutingServiceProvider" --tag="config"`

## Routing

SPA Routing starts with by defining your page routes as 'phase' routes. Traditionally these are placed in `routes/web.php`. In a regular app these would be 'Route::get' routes whose controller returns a view(). In a Phase app, just change it to `Route::phase`, and change your controller so that it returns `Phase::view()`. Modifying the [basic controller laravel example](https://laravel.com/docs/6.x/controllers#basic-controllers):
```diff
<?php

namespace App\Http\Controllers;

+ use Phased\Routing\Facades\Phase;
+ use Phased\State\Facades\Vuex;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
-    public function show($id)
+    public function UserProfile($id)
    {
-        return view('user.profile', ['user' => User::findOrFail($id)]);
+        Vuex::state([ 'user' => User::findOrFail($id) ]);
+        return Phase::view();
    }
}
```

And defining the route:
```diff
-Route::get('user/{id}', 'UserController@show');
+Route::phase('user/{id}', 'UserController@UserProfile');
```

Now navigating to `/user/{id}` will display `resources/js/pages/UserController/UserProfile.vue`, and the user with id `$id`, will be loaded into your vuex store at `this.$store.state.user`. Creating a second page and navigating between the two using [<router-link>](https://router.vuejs.org/api/#router-link) will automatically handle vuex store updating based on the data loaded in the controller, while using nice SPA page transitions.

To get a list of all registered phase routes, the command `php artisan phase:routes` will list a table similar to `route:list`.

## State Management
State Management from a Phase app is used through the `Vuex` facade provided, as well as the Collection, and Model Helpers. The Facade contains two primary data loading functions.

### State

```php
Vuex::state($state);
```
`state` accepts an array of values, which will be merged in/set as the base vuex state object.
```js
// Basic Vuex store.
export default new Store(hydrate({
  state: {
    count: 0,
    app: ''
  }
}))
console.log(this.$store.state.count) // 0
```
All or some of the keys can be updated at the same time.
```php
// From a controller or model
Vuex::state([ 'count' => 1 ]);
```

```js
console.log(this.$store.state.count) // 1
```

### Module

The other and perhaps more used variant is `Vuex::module($namespace, $data);`. This updates a [vuex module](https://vuex.vuejs.org/guide/modules.html) with the given data, much like how `::state` works.

```js
// Basic Vuex store.
export default new Store(hydrate({
  modules: {
    user: {
      state: {
        name: '',
      }
    },
    app: {
      modules: {
        options: {
          state: {
            version: '0.0.0'
          }
        }
      }
    }
  }
}))

console.log(this.$store.state.user.name) // ''
console.log(this.$store.state.app.options.version) // '0.0.0'
```

```php
Vuex::module('user', [ 'name' => 'Reed' ]);

// Nested Modules
Vuex::module('app/options', [ 'version' => '0.0.4' ]);
```
```js
console.log(this.$store.state.user.name) // 'Reed'
console.log(this.$store.state.app.options.version) // '0.0.4'
```

### Collections

Out of the box, Collections have been extended so that they now have a `->toVuex` method. This takes two arguments, the vuex namespace, and the key in which to save the data. Take the following example.
```js
export default new Store(hydrate({
  modules: {
    flights: {
      state: {
        selected: null,
        in_flight: []
      }
    }
  }
}))
```
```php
App\Flight::query()
  ->where('in_flight', true)
  ->get()
  ->toVuex('flights', 'in_flight');
```

### Models

Much like Collections, Models can have a ->toVuex method, this however is applied via a trait, and not available out of the box.
```php
// Flight Model
use Phased\State\Traits\Vuexable;

class Flight extends Model
{
    use Vuexable;
}

// Elsewhere...
App\Flight::find(5)->toVuex('flights', 'selected');
```

### Mutations
Although the above approaches cover a wide range of use cases, sometimes a bit more finesse may be required. For a bit more control, the `Vuex::commit($mutation, $value);` is provided. This allows full control for calling your vuex mutations from your controllers. These mutations will be called _after_ all the 'automatic' mutations above (`toVuex`, `::module`, `::state`), however the order in which the mutations are called cannot be relied upon.
```php
use Phased\State\Facades\Vuex;

Vuex::commit('SET_COUNT', 5);
Vuex::commit('user/SET_USER', Auth::user());
```

### Actions
Much like mutations above, Actions can be called using the `dispatch` method.
```php
use Phased\State\Facades\Vuex;

Vuex::dispatch('increment', 5);
Vuex::dispatch('user/setActive', Auth::user());
```

### Module Loaders
Very often you will want your vuex modules to be loaded in the same way. Phase provides the concept of Module Loaders for this purpose. A Module Loader is associated with a vuex module on the front end, and a method can be created for each root level key in that modules state. All Module Loaders following the naming convention of `app/VuexLoaders/{namespace}ModuleLoader.php` will get automatically discovered, however you can register any class manually in the `boot` method of your application's `AppServiceProvider`.

```php
use Phased\State\Facades\Vuex;

// Custom module registration
Vuex::register([
  MyVuexModuleLoader::class,
]);
```

The Vuex namespace will be guessed based on the class name, and naming conventions, however if needed, the namespace can be specified by adding `protected $namespace = 'app/users';`

A Module Loader is likely best explained through examples. Given the following `users` vuex module:
```js
// 'users' Vuex Module
const state = {
  all: [],
  active: null,
  count: 0
}
```
A matching Module Loader could be written as
```php
// app/VuexLoaders/UsersModuleLoader.php
namespace App\VuexLoaders;

use App\User;
use Illuminate\Support\Facades\Auth;
use Phased\State\Support\VuexLoader as ModuleLoader;

class AppModuleLoader extends ModuleLoader
{
  /**
   * Gets a list of all available users
   *
   * @return \Illuminate\Support\Collection
   */
  public function all()
  {
    return User::select('id', 'name')->get();
  }

  /**
   * Gets the details for the requested user
   *
   * @return App\User
   */
  public function active($id)
  {
    return User::find($id);
  }

  /**
   * Gets the total count of all the users in the system
   *
   * @return int
   */
  public function count()
  {
    return User::count();
  }
}
```

Now anytime you need to fetch this data, it can be called using the `load` or `lazyLoad` methods.
```php
use Phased\State\Facades\Vuex;

// Loads all users into users module at $store.state.users.all
Vuex::load('users', 'all');
// Loads user 1 into users.active
Vuex::load('users', 'active', 1);
// Load multiple keys at once
Vuex::load('users', [
  'all',
  'active' => 1,
  'count'
]);
```

In some cases you may need to lazy load the data. A common use case for this is attaching key user details on page load. You might try to accomplish this by adding something like the following in `AppServiceProvider`.
```php
// AppServiceProvider
public function boot()
{
  if (!request()->expectsJson()) {
    Vuex::load('auth', 'user');
  }
}

// AuthModuleLoader.php
public function user()
{
  return Auth::user();
}
```
However since this runs before the auth middleware, `Auth::user()` will always return null. This is easily fixed using 'lazy loading'. Normally data is eagerly loaded when the function is called, with Lazy Loading however the data is put in queue and not loaded until the final response is being formed.
```php
Vuex::lazyLoad('auth', 'user');
```

### Lazy Loading
In addition to the Module Loader `::lazyLoad` convenience method, any data can be lazy loaded by providing a function which returns the data instead of the data itself.

```php
// Lazy load the entire object
Vuex::module('user', fn() => ['active' => Auth::user() ] );

// Lazy Load a single key
Vuex::module('user', [
  'active' => function () {
    return Auth::user();
  }
]);

// These work with state too
Vuex::state(['number' => fn() => 5]);
Vuex::state(function () {
  return ['number' => 5]
});
```

## Server Side Rendering
Server side rendering (SSR) is disabled out of the box, due to the fact that writing "universal" or "isomorphic" javascript can be a little more complex than standard Javascript, however there are great resources out there if you are curious: https://ssr.vuejs.org/guide/universal.html . To enable SSR in your Phase app, first add `NODE_PATH=` to your .env with the path to your node binary. Then simply update your `ssr` option in your config to be true. Now when you 'view source' & refresh the page you should see the raw html instead of the standard `<div id="app"></div>`. The other option available is `hydrate`. With this set to true, your vue app will be 'hydrated' with all the interactive elements & components you would expect from a vue app. When set to false, no Javascript is loaded on your page which depending on your goals may or may not be what you are looking for. Although a little out of date, and example of a non-hydrated app (albeit not built with phase) is [Netflix circa 2017](https://jakearchibald.com/2017/netflix-and-react/). Phase attempts to remove the barrier and make SSR easy, however there are a few rules you still need to follow in order to successfully write a universal app. The most important and easy to forget is you can no longer rely on the browser global api to be available. That means when the app is run on the server, there is no `window` or `document`,

## Troubleshooting
  - `Vuex::dd();` or `dd(Vuex::toArray());` will dump all the currently saved vuex state
  - For Api calls, any mutations will (should) be visible from within the Vue DevTools mutations tab

## Example
To kick things off with a basic example, lets create a simple controller and load the first page of our new app.
```sh
php artisan make:controller PhaseController
```

Now go to your `routes/web.php` and add your first route
```php
//routes/web.php
Route::phase('/', 'PhaseController@HomePage');
```

Now open up the controller and create the `HomePage` method. Notice the return statement. `Phase::view()` handle syncing the correct data flow for the page routes. It will automatically switch between loading the page, and just updating your vuex state with the appropriate data. If the method is strictly for API calls, then returning only the updated vuex state with `return response()->vuex();` will suffice, however for now, its a page load so `Phase::view();` it is.
```php
<?php

namespace App\Http\Controllers;

use Phased\Routing\Facades\Phase;
use Phased\State\Facades\Vuex;

class PhaseController extends Controller
{
    public function HomePage()
    {
        return Phase::view();
    }
}
```

Now run laravel-mix and load the page. If all went well, The required .vue files have been generated if they didn't already exist, and the page should load up.
