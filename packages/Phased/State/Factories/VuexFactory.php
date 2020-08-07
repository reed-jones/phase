<?php

declare(strict_types=1);

namespace Phased\State\Factories;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Str;
use JsonSerializable;
use Phased\State\Exceptions\VuexInvalidKeyException;
use Phased\State\Exceptions\VuexInvalidModuleException;
use Phased\State\Exceptions\VuexInvalidStateException;
use Phased\State\Facades\Vuex;
use Phased\State\Traits\VuexFactoryDependencyResolverTrait;

class VuexFactory implements Arrayable, Jsonable, JsonSerializable
{
    use VuexFactoryDependencyResolverTrait;

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @var array Base (non-namespaced) vuex state.
     * @var array Base (non-namespaced) lazily evaluated vuex state.
     */
    protected $_state = [],
        $_lazyState = [];

    /**
     * @var array Vuex modules.
     * @var array Lazily evaluated Vuex modules.
     */
    protected $_modules = [],
        $_lazyModules = [];

    /**
     * @var array Mutations to be committed.
     * @var array Lazy Evaluated mutations to be committed.
     */
    protected $_mutations = [],
        $_lazyMutations = [];

    /**
     * @var array Actions to be dispatched.
     * @var array Lazily Evaluated actions to be dispatched.
     */
    protected $_actions = [],
        $_lazyActions = [];

    /** @var array Registered Classes for vuex ModuleLoaders. */
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

                    $this->container->singleton($location, function () use ($loader) {
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
     * @param string $namespace
     * @param string|array $keys
     * @param mixed $args
     * @return $this
     */
    public function load(string $namespace, $keys, ...$args)
    {
        $this->loadModule($namespace, $keys, $args, false);

        return $this;
    }

    /**
     * ModuleLoader LazyLoad Function.
     * @param string $namespace
     * @param string|array $keys
     * @param mixed $args
     * @return $this
     */
    public function lazyLoad($namespace, $keys, ...$args)
    {
        $this->loadModule($namespace, $keys, $args, true);

        return $this;
    }

    /**
     * Internal Module Loader Function.
     * @param string $namespace
     * @param string|array $keys
     * @param array $args
     * @param bool $lazy
     * @return $this
     */
    protected function loadModule(string $namespace, $keys, array $args, bool $lazy)
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

        // $moduleLoader = $this->registeredMappings[$namespace]['class'];
        $moduleLoader = $this->container->make($this->registeredMappings[$namespace]['class']);

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

                $params = $this->resolveClassMethodDependencies( $args, $moduleLoader, $key );

                if ($lazy) {
                    Vuex::module(
                        $namespace,
                        [
                            $key => function () use ($moduleLoader, $key, $params) {
                                return $moduleLoader->{$key}(...array_values($params));
                            },
                       ]
                    );
                } else {
                    Vuex::module(
                        $namespace,
                        [$key => $moduleLoader->{$key}(...array_values($params))]
                    );
                }
            });
    }

    /**
     * Creates a new 'Vuex' class for easy $store access.
     * @deprecated please use state(), module(), or toVuex() instead
     * @param Closure $callable
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
     * @param \Illuminate\Support\Collection|array $state
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
     * @param string $namespace
     * @param \Illuminate\Support\Collection|array $state
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

    public function recursiveGet(array $selectors, array $data) {
        // Grab the first item
        $first = array_shift($selectors);

        if (isset($data['state'][$first])) {
            // return from state if possible
            return $data['state'][$first];
        } else if (isset($data['modules'][$first])) {
            // pass to nested module and check its state
            return $this->recursiveGet($selectors, $data['modules'][$first]);
      }

      // all unfound items will happily return null (no errors)
      return null;
    }

    /**
     * Usage: to get this.$store.state.users.active.name
     * Vuex::get('users.active.name')
     */
    public function get(string $selector) {
        $parts = explode('.', $selector);
        return $this->recursiveGet($parts, $this->toArray());
    }

    public function reduceData($a, $b, $reduce)
    {
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
