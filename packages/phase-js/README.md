## packages

### JavaScript
- [ ] laravel mix plugin
- [ ] webpack plugin (routes generator)
- [ ] phase-utilities? (tree shaken...)
- [ ] vuexcellent as it is now (legacy I guess)
- [ ] vuexcellent-hydrate (merge store objects, inject plugin) if !store.plugin { plugins:[vuexcellentPlugin] } else store.plugins.every(p => p.name !== 'vuexcellentPlugin') ? store.plugins:[vuexcellentPlugin, ...store.plugins]
- [ ] vuexcellent-interceptor


### PHP
- [ ] vuexcellent Vuex factory
    - store new or merge/replace
        - Models
        - Collections
        - Primitives
    - lazy evaluation
        - for all of the above types
        - full functions for state/module
        - partial functions for state/module
        - merge resolution for full/partial functions
- [ ] Phase Routes
    - auto returns?
    - phase:routes command
    - (secretly) skip controller -> direct to vue component `Route::phase('/home', 'Auth/Dashboard.vue');
