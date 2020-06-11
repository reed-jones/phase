<?php

declare(strict_types=1);

namespace Phased\State\Factories;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use JsonSerializable;
use Phased\State\Exceptions\VuexInvalidKeyException;
use Phased\State\Exceptions\VuexInvalidModuleException;
use Phased\State\Exceptions\VuexInvalidStateException;
use Phased\State\Facades\Vuex;

class VuexFactory implements Arrayable, Jsonable, JsonSerializable
{
    // use CanRegisterModules,

    /**
     * Base (non-namespaced) vuex state.
     *
     * @var array
     */
    protected $_state = [];

    /**
     * Base (non-namespaced) lazily evaluated vuex state.
     *
     * @var array
     */
    protected $_lazyState = [];

    /**
     * Vuex modules.
     *
     * @var array
     */
    protected $_modules = [];

    /**
     * Lazily evaluated Vuex modules.
     *
     * @var array
     */
    protected $_lazyModules = [];

    /**
     * Mutations to be committed.
     *
     * @var array
     */
    protected $_mutations = [];

    /**
     * Lazy Evaluated mutations to be committed.
     *
     * @var array
     */
    protected $_lazyMutations = [];

    /**
     * Actions to be dispatched.
     *
     * @var array
     */
    protected $_actions = [];

    /**
     * Lazily Evaluated actions to be dispatched.
     *
     * @var array
     */
    protected $_lazyActions = [];

    /**
     * Registered Classes for vuex ModuleLoaders.
     *
     * @var array
     */
    protected $registeredMappings = [];

    /**
     * ModuleLoader Manual Registration.
     */
    public function register($mappings)
    {
        $this->registeredMappings = array_merge(
            $this->registeredMappings,
            collect($mappings)
                ->mapWithKeys(function ($location) {
                    $loader = new $location;

                    App::singleton($location, function () use ($loader) {
                        return $loader;
                    });

                    return [
                        $loader->getNamespace() => [
                            'class' => $location,
                            'methods' => get_class_methods($loader),
                        ],
                    ];
                })
                ->toArray()
        );
    }

    /**
     * ModuleLoader Load Function.
     */
    public function load($namespace, $keys, ...$args)
    {
        $this->loadModule($namespace, false, $keys, $args);

        return $this;
    }

    /**
     * ModuleLoader LazyLoad Function.
     */
    public function lazyLoad($namespace, $keys, ...$args)
    {
        $this->loadModule($namespace, true, $keys, $args);

        return $this;
    }

    /**
     * Internal Module Loader Function.
     */
    protected function loadModule($namespace, $lazy, $keys, $args)
    {
        if (is_string($keys)) {
            $keys = [$keys => $args];
        }

        if (! isset($this->registeredMappings[$namespace])) {
            throw new VuexInvalidModuleException("VuexLoader '{$namespace}' has not been properly registered.");
        }

        if (! is_array($keys)) {
            throw new VuexInvalidKeyException('Invalid keys were passed to Vuex::load.');
        }

        $moduleLoader = App::make($this->registeredMappings[$namespace]['class']);

        collect($keys)
            ->mapWithKeys(function ($value, $key) {
                return is_int($key) ? [$value => null] : [$key => $value];
            })
            ->each(function ($args, $key) use ($namespace, $moduleLoader, $lazy) {
                if (! in_array($key, $this->registeredMappings[$namespace]['methods'])) {
                    throw new VuexInvalidKeyException("Method '{$key}' does not exist on '{$this->registeredMappings[$namespace]['class']}'");
                }

                if (! is_array($args)) {
                    $args = [$args];
                }

                if ($lazy) {
                    Vuex::module(
                        $namespace,
                        [
                            $key => function () use ($moduleLoader, $key, $args) {
                                return $moduleLoader->{$key}(...$args);
                            },
                       ]
                    );
                } else {
                    Vuex::module(
                        $namespace,
                        [$key => $moduleLoader->{$key}(...$args)]
                    );
                }
            });
    }

    /**
     * Creates a new 'Vuex' class for easy $store access.
     *
     * @deprecated please use state(), module(), or toVuex() instead
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
        [$isLazy, $newState] = $this->verifyState($state);

        if ($isLazy) {
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
        if (! is_string($namespace) || empty($namespace)) {
            throw new VuexInvalidModuleException('$namespace must be a string.');
        }

        [$isLazy, $newState] = $this->verifyState($state);

        if ($isLazy) {
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

        if (! empty($this->_state) || ! empty($this->_lazyState)) {
            $store['state'] = [];
        }

        if (! empty($this->_modules) || ! empty($this->_lazyModules)) {
            $store['modules'] = [];
        }

        if (! empty($this->_mutations) || ! empty($this->_lazyMutations)) {
            $store['mutations'] = [];
        }

        if (! empty($this->_actions) || ! empty($this->_lazyActions)) {
            $store['actions'] = [];
        }

        if (! empty($this->_state)) {
            $store['state'] = $this->reduceData($this->_state, $store['state'], function ($acc, $cur) {
                return array_merge_phase($acc, $this->generateState($cur));
            });
        }

        if (! empty($this->_lazyState)) {
            $store['state'] = $this->reduceData($this->_lazyState, $store['state'], function ($acc, $cur) {
                return array_merge_phase($acc, $this->generateLazyState($cur));
            });
        }

        if (! empty($this->_modules)) {
            foreach ($this->_modules as $module) {
                $store['modules'] = array_merge_phase($store['modules'], $this->generateNamespacedModules($module));
            }
        }

        if (! empty($this->_lazyModules)) {
            foreach ($this->_lazyModules as $module) {
                $store['modules'] = array_merge_phase($store['modules'], $this->generateLazyNamespacedModules($module));
            }
        }

        if (! empty($this->_mutations)) {
            $store['mutations'] = $this->_mutations;
        }

        if (! empty($this->_lazyMutations)) {
            foreach ($this->_lazyMutations as $mutation => $value) {
                array_push($store['mutations'], [$mutation, $value()]);
            }
        }

        if (! empty($this->_actions)) {
            $store['actions'] = $this->_actions;
        }

        if (! empty($this->_lazyActions)) {
            foreach ($this->_lazyActions as $action => $value) {
                array_push($store['actions'], [$action, $value()]);
            }
        }

        return $store;
    }

    public function reduceData($a, $b, $reduce) {
        foreach ($a as $state) {
            $b = $reduce($b, $state);
        }
        return $b;
    }

    /**
     * dd the current state.
     */
    public function dd()
    {
        dd($this->toArray());
    }

    /**
     * Alias for toArray.
     *
     * @deprecated
     */
    public function asArray()
    {
        return $this->toArray();
    }

    /**
     * Alias for toJson.
     *
     * @deprecated
     */
    public function asJson($options = 0)
    {
        return $this->toJson($options);
    }

    /**
     * Alias for toResponse.
     *
     * @deprecated
     */
    public function asResponse()
    {
        return $this->toResponse();
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Returns the currently saved data as a json string.
     *
     * @return string|false
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Returns the current vuex data as a json response to
     * be picked up by the front end.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse()
    {
        return response()->json(['$vuex' => $this->toArray()]);
    }

    /**
     * Verifies the supplied state, and normalizes
     * it to its basic callable/arrays roots.
     */
    public function verifyState($state)
    {
        if (method_exists($state, 'toArray')) {
            $state = $state->toArray();
        } elseif (is_array($state)) {
            $state = collect($state)->toArray();
        } elseif (! is_callable($state)) {
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

    /**
     * Checks if some items in the array pass the predicate.
     */
    protected function array_some($arr, callable $callback)
    {
        foreach ($arr as $ele) {
            if (call_user_func($callback, $ele)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generates State.
     */
    protected function generateState($state)
    {
        return $state;
    }

    /**
     * Generates Lazy State.
     */
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

    /**
     * Generates Modules.
     */
    protected function generateNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (! Str::contains($namespace, '/')) { // simple module namespace
                return [$namespace => ['state' => $state]];
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

    /**
     * Generates Lazy Modules.
     */
    protected function generateLazyNamespacedModules($module)
    {
        foreach ($module as $namespace => $state) {
            if (! Str::contains($namespace, '/')) { // simple module namespace
                if (is_callable($state)) {
                    $state = $state();
                }

                foreach ($state as $key => $value) {
                    if (is_callable($value)) {
                        $state[$key] = $value();
                    }
                }

                return [$namespace => ['state' => $state]];
            } else { // complex nested modules namespace
                $namespaces = array_reverse(collect(explode('/', $namespace))->toArray());

                // final state is starting value
                $arr = $state;
                // build array in reverse
                foreach ($namespaces as $idx => $key) {
                    if (is_callable($arr)) {
                        $arr = $arr();
                    }

                    foreach ($arr as $arrKey => $arrValue) {
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

    // protected function generateMutations($mutation)
    // {
    //     return $mutation;
    // }

    // protected function generateLazyMutations()
    // {
    //     return [];
    // }

    // protected function generateActions()
    // {
    //     return [];
    // }

    // protected function generateLazyActions()
    // {
    //     return [];
    // }

    /**
     * Dispatch an action on the front end.
     */
    public function dispatch($action, $value = null)
    {
        if (! is_string($action) || empty($action)) {
            throw new VuexInvalidModuleException('$mutation must be a string.');
        }

        if (! isset($value)) {
            array_push($this->_actions, [$action]);
        } elseif (is_string($value) || is_bool($value) || is_numeric($value)) {
            array_push($this->_actions, [$action, $value]);
        } else {
            [$isLazy, $newValue] = $this->verifyState($value);
            if ($isLazy) {
                array_push($this->_lazyActions, [$action, $newValue]);
            } else {
                array_push($this->_actions, [$action, $newValue]);
            }
        }

        return $this;
    }

    /**
     * Commits a mutation on the front end.
     */
    public function commit($mutation, $value = null)
    {
        if (! is_string($mutation) || empty($mutation)) {
            throw new VuexInvalidModuleException('$mutation must be a string.');
        }

        if (! isset($value)) {
            array_push($this->_mutations, [$mutation]);
        } elseif (is_string($value) || is_bool($value) || is_numeric($value)) {
            array_push($this->_mutations, [$mutation, $value]);
        } else {
            [$isLazy, $newValue] = $this->verifyState($value);
            if ($isLazy) {
                array_push($this->_lazyMutations, [$mutation, $newValue]);
            } else {
                array_push($this->_mutations, [$mutation, $newValue]);
            }
        }

        return $this;
    }
}
