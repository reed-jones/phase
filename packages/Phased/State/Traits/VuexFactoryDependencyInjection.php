<?php

namespace Phased\State\Traits;

use Illuminate\Support\Arr;
use Phased\State\Exceptions\VuexMissingRequiredParameter;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Modified from Laravel's Route Resolver
 *
 * https://github.com/laravel/framework/blob/6.x/src/Illuminate/Routing/RouteDependencyResolverTrait.php
 */
trait VuexFactoryDependencyInjection
{
    /**
     * Resolve the object method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  object  $instance
     * @param  string  $method
     * @return array
     */
    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (! method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->resolveMethodDependencies(
            $parameters, new ReflectionMethod($instance, $method)
        );
    }

    /**
     * Resolve the given method's type-hinted dependencies.
     *
     * @param  array  $parameters
     * @param  \ReflectionFunctionAbstract  $reflector
     * @return array
     */
    public function resolveMethodDependencies(array $parameters, ReflectionFunctionAbstract $reflector)
    {
        $resolvedParameters = [];

        $values = array_values($parameters);

        $skippableValue = new \stdClass;

        foreach ($reflector->getParameters() as $key => $parameter) {
            $instance = $this->transformDependency($parameter, $parameters, $skippableValue);

            if ($instance !== $skippableValue) {
                $this->spliceIntoParameters($resolvedParameters, $key, [$instance]);
            } else if($parameter->isVariadic()) {
                $this->spliceIntoParameters($resolvedParameters, $key, $values, count($values) - 1);
            } else if (isset($parameters[$parameter->getName()])) {
                $this->spliceIntoParameters($resolvedParameters, $key, [$parameters[$parameter->getName()]]);
            }
        }

        return $resolvedParameters;
    }

    /**
     * Attempt to transform the given parameter into a class instance. (model, or from container)
     *
     * @param  \ReflectionParameter  $parameter
     * @param  array  $parameters
     * @param  object  $skippableValue
     * @return mixed
     */
    protected function transformDependency(ReflectionParameter $parameter, $parameters, $skippableValue)
    {
        $class = $parameter->getClass();
        if ($class && ! $this->alreadyInParameters($class->name, $parameters)) {

            if ($this->container->bound($class->getName())) {
                return $this->container->make($class->name);
            }

            if (!isset($parameters[$parameter->getName()])) {
                throw new VuexMissingRequiredParameter("Missing required parameter {$parameter->getName()}");
            }

            if(method_exists($class->name, 'getRouteKeyName')) {
                $maybeModel = new $class->name;
                return $maybeModel->where($maybeModel->getRouteKeyName(), $parameters[$parameter->getName()])->firstOrFail();
            }
        }

        return $skippableValue;
    }

    /**
     * Determine if an object of the given class is in a list of parameters.
     *
     * @param  string  $class
     * @param  array  $parameters
     * @return bool
     */
    protected function alreadyInParameters($class, array $parameters)
    {
        return ! is_null(Arr::first($parameters, function ($value) use ($class) {
            return $value instanceof $class;
        }));
    }

    /**
     * Splice the given value into the parameter list.
     *
     * @param  array  $parameters
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    protected function spliceIntoParameters(array &$parameters, $offset, $value, $count = 0)
    {
        array_splice($parameters, $offset, $count, $value);
    }
}
