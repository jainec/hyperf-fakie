<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Fakie.
 *
 * @link     https://github.com/jainec
 * @document https://github.com/jainec/hyperf-fakie/blob/master/README.md
 * @contact  @jaineccs
 * @license  https://github.com/jainec/hyperf-fakie/blob/master/LICENSE
 */
namespace JaineC\Hyperf\Fakie;

use JaineC\Hyperf\Fakie\Exception\FakieException;
use ReflectionClass;

class Fakie
{
    protected const DEFAULT_VALUE = 'default';

    private string $build_method;

    public function __construct(
        private string $class_name,
    ) {
        $this->validateClassName();
    }

    public static function object(string $class_name): static
    {
        return new self($class_name);
    }

    public function setBuildMethod(string $build_method): static
    {
        $this->build_method = $build_method;
        return $this;
    }

    public function create(array $override_properties = [])
    {
        $properties = $this->getClassProperties();

        $populated_properties = $this->populateClassProperties($properties, $override_properties);

        return $this->mountObject($populated_properties);
    }

    /**
     * @throws FakieException
     */
    private function validateClassName(): void
    {
        if (! isset($this->class_name)) {
            throw new FakieException('The object class was not defined. Please set the object class using Fakie::object()');
        }

        if (empty($this->class_name)) {
            throw new FakieException('The object class name is invalid');
        }
    }

    /**
     * @throws FakieException
     */
    private function getClassProperties(): array
    {
        try {
            $class = new ReflectionClass($this->class_name);

            $properties = $class->getConstructor()?->getParameters();

            if (isset($this->build_method)) {
                $properties = $class->getProperties();
            }

            if (is_null($properties)) {
                throw new FakieException();
            }

            return $properties;
        } catch (\Exception $e) {
            throw new FakieException("Error trying to fetch class properties. Maybe your class has no constructor/properties/build method: {$e->getMessage()}");
        }
    }

    private function populateClassProperties(array $properties, array $override_properties): array
    {
        $populated_properties = [];

        foreach ($properties as $property) {
            $property_name = $property->getName();

            $populated_properties[$property_name] = match (true) {
                $this->isValueInOverrideProperties($override_properties, $property_name) => $override_properties[$property_name],
                $this->isValueInConfigRules($property_name) => $this->getValueFromConfigRules($property_name),
                default => $this->generateRandomValueByType($property),
            };
        }

        return $populated_properties;
    }

    private function isValueInOverrideProperties(array $override_properties, string $property): bool
    {
        if (in_array($property, array_keys($override_properties))) {
            return true;
        }

        return false;
    }

    private function isValueInConfigRules(string $property): bool
    {
        $config_rules = config('fakie.rules');

        $rule = $config_rules[$this->class_name][$property] ?? null;

        if (! isset($rule)) {
            return false;
        }

        return true;
    }

    private function getValueFromConfigRules(string $property)
    {
        $config_rules = config('fakie.rules');

        $rule = $config_rules[$this->class_name][$property];

        if (is_a($rule, Fakie::class)) {
            $rule = $rule->create();
        }

        return $rule;
    }

    /**
     * @throws FakieException
     */
    private function generateRandomValueByType($property)
    {
        try {
            $property_type = $property->getType()?->getName();

            if (! isset($property_type)) {
                return self::DEFAULT_VALUE;
            }

            if (interface_exists($property_type)) {
                throw new FakieException("The property {$property->getName()} is an interface type");
            }

            if (class_exists($property_type)) {
                return $this->handleClass($property_type);
            }

            return $this->getValueByType($property_type);
        } catch (\Exception $e) {
            throw new FakieException($e->getMessage());
        }
    }

    /**
     * @throws FakieException
     * @throws \ReflectionException
     */
    protected function handleClass(string $property_type): ?object
    {
        $class = new ReflectionClass($property_type);

        if ($class->isAbstract()) {
            throw new FakieException("It's not possible to instantiate the abstract property type {$property_type}");
        }

        return Fakie::object($property_type)->create();
    }

    protected function getValueByType(string $property_type)
    {
        return match ($property_type) {
            'string' => $this->generateRandomString(),
            'bool' => $this->generateRandomBoolean(),
            'float' => $this->generateRandomFloat(),
            'array' => $this->generateRandomArray(),
            default => $this->generateRandomInteger(),
        };
    }

    protected function generateRandomString(): string
    {
        return str_shuffle('abcdefghijklmnopqrstuvwxyz');
    }

    protected function generateRandomBoolean(): bool
    {
        return (bool) rand(0, 1);
    }

    protected function generateRandomFloat(): float
    {
        return (float) rand(1, 9999) / 100;
    }

    protected function generateRandomArray(): array
    {
        return ['test1', 'test2'];
    }

    protected function generateRandomInteger(): int
    {
        return rand();
    }

    /**
     * @throws FakieException
     */
    private function mountObject(array $populated_properties)
    {
        try {
            $class = new ReflectionClass($this->class_name);

            if (isset($this->build_method)) {
                $instance = $class->newInstance();
                $class->getMethod($this->build_method)->invoke($instance, $populated_properties);
                return $instance;
            }

            return $class->newInstanceArgs($populated_properties);
        } catch (\Exception $e) {
            throw new FakieException("It was not possible to mount the object using {$this->build_method} method: {$e->getMessage()}");
        }

    }
}
