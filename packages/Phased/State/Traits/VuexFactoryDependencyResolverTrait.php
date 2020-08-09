<?php

namespace Phased\State\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\RouteDependencyResolverTrait;
use Phased\State\Exceptions\VuexMissingRequiredParameter;
use ReflectionFunctionAbstract;
use ReflectionParameter;

/**
 * Modified from Laravel's Route Resolver
 * https://github.com/laravel/framework/blob/6.x/src/Illuminate/Routing/RouteDependencyResolverTrait.php
 *
 * TODO: Will need to update 'transformDependency' as outlined here. this will require a bump in the minimum Laravel version
 * but will help prepare for php 8.0 https://github.com/laravel/framework/pull/33039
 */
trait VuexFactoryDependencyResolverTrait
{
    /**
     * Extends Laravel's Route Model binding/dependency injection
     */
    use RouteDependencyResolverTrait;

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
            } else if (isset($parameters[$parameter->getPosition()]))  {
                $this->spliceIntoParameters($resolvedParameters, $key, [$parameters[$parameter->getPosition()]]);
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

            if(class_exists($class->name)) {
                $model = new $class->name;
                if ($model instanceof Model) {
                    return $model->resolveRouteBinding($parameters[$parameter->getName()]);
                }
            }
        }

        return $skippableValue;
    }

    /**
     * Splice the given value into the parameter list.
     *
     * Overridden from RouteDependencyResolverTrait to allow 'count' arguemnt
     * to allow for variadic arguments
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
