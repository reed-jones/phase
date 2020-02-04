<?php

declare(strict_types=1);

namespace Phased\State\Factories;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Phased\State\Exceptions\VuexInvalidKeyException;
use Phased\State\Exceptions\VuexInvalidModuleException;
use Phased\State\Exceptions\VuexInvalidStateException;
use Phased\State\Facades\Vuex;
use ReflectionClass;

class VuexFactory
{
    /**
     * Base (non-namespaced) vuex state.
     *
     * @var array
     */
    private $_state = [];

    /**
     * Base (non-namespaced) lazily evaluated vuex state.
     *
     * @var array
     */
    private $_lazyState = [];

    /**
     * Vuex modules.
     *
     * @var array
     */
    private $_modules = [];

    /**
     * Lazily evaluated Vuex modules.
     *
     * @var array
     */
    private $_lazyModules = [];

    private $registeredMappings = [];

    public function register($mappings)
    {
        $this->registeredMappings = collect($mappings)->mapWithKeys(function ($location) {

            $loader = new $location;

            App::singleton($location, function () use ($loader) {
                return $loader;
            });

            return [
                $loader->getNamespace() => [
                    'class' => $location,
                    'methods' => get_class_methods($loader)
                ]
            ];
        });
    }

    public function load($namespace, $key, ...$args)
    {
        if (!isset($this->registeredMappings[$namespace])) {
            throw new VuexInvalidModuleException("VuexLoader '{$namespace}' has not been properly registered.");
        }

        if (!in_array($key, $this->registeredMappings[$namespace]['methods'])) {
            throw new VuexInvalidKeyException("Method '{$key}' does not exist on '{$this->registeredMappings[$namespace]['class']}'");
        }

        Vuex::module($namespace, [
            $key => App::make($this->registeredMappings[$namespace]['class'])->{$key}(...$args)
        ]);
    }

    /**
     * Creates a new 'Vuex' class for easy $store access.
     *
     * @param Closure $callable
     *
     * @return void
     */
    public function store(Closure $closure)
    {
        // call the closure, injecting a new store instance
        // for state/module creation
        // left for historical purposes, unlikely to be used moving forward
        $closure($this);

        // Examples... this call is effectively replaced by:
        // Vuex::state(['all' => User::all()]);
        // Vuex::module('users', ['all' => User::all()]);
    }

    /**
     * Sets or adds to the base vuex state.
     *
     * @param \Illuminate\Support\Collection|array $state
     *
     * @return void
     */
    public function state($state)
    {
        [$lazy, $newState] = $this->verifyState($state);

        if ($lazy) {
            array_push($this->_lazyState, $newState);
        } else {
            array_push($this->_state, $newState);
        }

        return $this;
    }

    /**
     * Creates or Updates a new vuex module.
     *
     * @param string $namespace
     * @param \Illuminate\Support\Collection|array $state
     *
     * @return void
     */
    public function module(string $namespace, $state)
    {
        if (!is_string($namespace) || empty($namespace)) {
            throw new VuexInvalidModuleException('$namespace must be a string.');
        }

        [$lazy, $newState] = $this->verifyState($state);

        if ($lazy) {
            array_push($this->_lazyModules, [$namespace => $newState]);
        } else {
            array_push($this->_modules, [$namespace => $newState]);
        }

        return $this;
    }

    /**
     * Returns the currently saved data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $store = [];

        // TODO: generate actions for each namespace/key: App/VuexLoader/Users::users() => users/refreshUsers

        if (!empty($this->_state) || !empty($this->_lazyState)) {
            $store['state'] = [];
        }

        if (!empty($this->_modules) || !empty($this->_lazyModules)) {
            $store['modules'] = [];
        }

        if (!empty($this->_state)) {
            foreach ($this->_state as $state) {
                $store['state'] = array_merge_phase($store['state'], $this->generateState($state));
            }
        }

        if (!empty($this->_modules)) {
            foreach ($this->_modules as $module) {
                $store['modules'] = array_merge_phase($store['modules'], $this->generateNamespacedModules($module));
            }
        }

        if (!empty($this->_lazyState)) {
            foreach ($this->_lazyState as $state) {
                $store['state'] = array_merge_phase($store['state'], $this->generateLazyState($state));
            }
        }

        if (!empty($this->_lazyModules)) {
            foreach ($this->_lazyModules as $module) {
                $store['modules'] = array_merge_phase($store['modules'], $this->generateLazyNamespacedModules($module));
            }
        }

        return $store;
    }
    /**
     * Alias for toArray
     */
    public function asArray()
    {
        return $this->toArray();
    }

    /**
     * Returns the currently saved data as a json string.
     *
     * @return string|false
     */
    public function toJson()
    {
        return json_encode($this->asArray());
    }

    /**
     * Alias for toJson
     */
    public function asJson()
    {
        return json_encode($this->asArray());
    }

    /**
     * Returns the current vuex data as a json response to
     * be picked up by the front end.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse()
    {
        return response()->json(['$vuex' => $this->asArray()]);
    }

    /**
     * Alias for toResponse
     */
    public function asResponse()
    {
        return $this->toResponse();
    }

    protected function verifyState($state) {
        if (method_exists($state, 'toArray')) {
            $state = $state->toArray();
        } elseif (is_array($state)) {
            $state = collect($state)->toArray();
        } else if (!is_callable($state)) {
            throw new VuexInvalidStateException('$state must be an array or a Collection.');
        }

        foreach ($state as $key => $value) {
            if (method_exists($value, 'toArray')) {
                $state[$key] = $value->toArray();
            }
        }

        if (is_callable($state) || $this->array_some(array_values($state), function ($el) {
            return is_callable($el);
        })) {
            return [true, $state];
        } else {
            return [false, $state];
        }

    }

    protected function array_some($arr, callable $callback)
    {
        foreach ($arr as $ele) {
            if (call_user_func($callback, $ele)) {
                return true;
            }
        }

        return false;
    }

    protected function generateState($state)
    {
        return $state;
    }

    protected function generateLazyState($state)
    {
        $state = is_callable($state) ? $state() : $state;

        foreach ($state as $key => $value) {
            if (is_callable($value)) {
                $state[$key] = $value();
            }
        }
        return $state;
    }

    protected function generateNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (!Str::contains($namespace, '/')) { // simple module namespace
                return [$namespace => ['state' => $state] ];
            } else { // complex nested modules namespace
                $namespaces = array_reverse(collect(explode('/', $namespace))->toArray());
                // final state is starting value
                $arr = $state;
                // build array in reverse
                foreach ($namespaces as $idx => $key) {
                    $type = $idx === 0 ? 'state' : 'modules';
                    $arr = [$key => [$type => $arr]];
                }

                return $arr;
            }
        }
    }


    protected function generateLazyNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (!Str::contains($namespace, '/')) { // simple module namespace
                if (is_callable($state)) {
                    $state = $state();
                }

                foreach($state as $key => $value) {
                    if (is_callable($value)) {
                        $state[$key] = $value();
                    }
                }

                return [$namespace => ['state' => $state] ];
            } else { // complex nested modules namespace
                $namespaces = array_reverse(collect(explode('/', $namespace))->toArray());

                // final state is starting value
                $arr = $state;
                // build array in reverse
                foreach ($namespaces as $idx => $key) {

                    if (is_callable($arr)) {
                        $arr = $arr();
                    }

                    foreach($arr as $arrKey => $arrValue) {
                        if (is_callable($arrValue)) {
                            $arr[$arrKey] = $arrValue();
                        }
                    }

                    $type = $idx === 0 ? 'state' : 'modules';
                    $arr = [$key => [$type => $arr]];
                }

                return $arr;
            }
        }
    }
}
