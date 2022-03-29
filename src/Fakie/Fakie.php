<?php

declare(strict_types=1);

namespace Fakie;

use ReflectionClass;
use ReflectionProperty;

class Fakie
{
    private const FILL_NULL = 'fill_null';

    private const FILL_RANDOM = 'fill_random';

    private array $class_arguments = [];

    private string $filling_strategy = Fakie::FILL_RANDOM;

    private ?string $build_method = null;

    public function __construct(
        private string $class_name,
    ) {
    }

    public static function object(string $class_name): static
    {
        return new self($class_name);
    }

    public function setFillingStrategy(string $filling_strategy): Fakie
    {
        $this->filling_strategy = $filling_strategy;
        return $this;
    }

    public function setBuildMethod(?string $build_method): Fakie
    {
        $this->build_method = $build_method;
        return $this;
    }

    public function create(array $override_attributes = [])
    {
        $this->validateClassName();

        $attributes = get_class_vars($this->class_name);

        foreach ($attributes as $key => $value) {
            if ($this->attributeShouldBeOverridden($override_attributes, $key)) {
                continue;
            }

            if ($this->attributeIsSetInConfigRules($key)) {
                continue;
            }

            $this->generateValueByStrategy($key);
        }

        return $this->mountObject();
    }

    /**
     * @throws \Exception
     */
    private function validateClassName(): void
    {
        if (! isset($this->class_name)) {
            throw new \Exception('The object was not defined. Please set the object using Fakie::object()');
        }

        if (empty($this->class_name)) {
            throw new \Exception('The object name is invalid.');
        }
    }

    private function attributeShouldBeOverridden($override_attributes, $key): bool
    {
        if (in_array($key, array_keys($override_attributes))) {
            $this->class_arguments[$key] = $override_attributes[$key];
            return true;
        }

        return false;
    }

    private function attributeIsSetInConfigRules($key): bool
    {
        $rules = config('fakie.rules');

        $rule = $rules[$this->class_name][$key] ?? null;

        if (is_a($rule, Fakie::class)) {
            $rule = $rule->create();
        }

        if (isset($rule)) {
            $this->class_arguments[$key] = $rule;
            return true;
        }

        return false;
    }

    private function generateValueByStrategy($key): void
    {
        if ($this->filling_strategy === Fakie::FILL_NULL) {
            $this->class_arguments[$key] = null;
            return;
        }

        $this->class_arguments[$key] = $this->generateValueByType($key);
    }

    private function generateValueByType(string $attribute)
    {
        $property = new ReflectionProperty($this->class_name, $attribute);
        $property_type = $property->getType()?->getName();

        if (interface_exists($property_type)) {
            return $this->handleInterface($property_type);
        }

        if (class_exists($property_type)) {
            return $this->handleClass($property_type);
        }

        return match ($property_type) {
            'string' => strval(rand(1, 10000)),
            'bool' => (bool) mt_rand(0, 1),
            'float' => mt_rand(1, 125) / 100,
            'array' => [],
            default => rand(1, 10000),
        };
    }

    private function handleInterface($property_type)
    {
        $concrete_class = $this->getOneConcreteImplementation($property_type);

        return Fakie::object($concrete_class)->setBuildMethod($this->build_method ?? null)->create();
    }

    private function handleClass($property_type)
    {
        $class = new ReflectionClass($property_type);

        if ($class->isAbstract()) {
            $concrete_class = $this->getOneConcreteImplementation($property_type);
            $property_type = $concrete_class;
        }

        return Fakie::object($property_type)->setBuildMethod($this->build_method ?? null)->create();
    }

    /**
     * @param mixed $property_type
     *
     * @return string
     * @throws \Exception
     */
    private function getOneConcreteImplementation(mixed $property_type): string
    {
        $return_class = null;

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, $property_type)) {
                $return_class = $class;
                break;
            }
        }

        if (! isset($return_class)) {
            throw new \Exception("No concrete implementation of {$property_type} was found in your code");
        }

        return $return_class;
    }

    private function mountObject()
    {
        if (isset($this->build_method)) {
            $obj = new $this->class_name();

            return call_user_func([$obj, $this->build_method], $this->class_arguments);
        }

        return new $this->class_name(...$this->class_arguments);
    }
}
