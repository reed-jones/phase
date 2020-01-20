# Vuexcellent

Vuexcellent is an easy to use way to load data from your Laravel backend to your Vuex managed front end.
For documentation & more detailed usage instructions visit [Vuexcellent](https://vuexcellent.netlify.com/)

This is the repo containing the Laravel component of Vuexcellent.

## Quick Install

1. Install via composer
```sh
composer require vuexcellent/laravel
```

2. Update the blade template
```html
<head>
  <title>{{ config('app.name') }}</title>
  @vuex
</head>
```

---

## Contributions
PR's are welcome to the [main repo](https://github.com/reed-jones/vuexcellent).

To run the test suite, run phpunit
```sh
./vendor/bin/phpunit tests
```
