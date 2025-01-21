<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Data;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Class StoryblokData
 * Represents a wrapper for handling and manipulating
 * structured data.
 * Implements Iterator, ArrayAccess, and Countable
 * for seamless data traversal, access, and manipulation.
 * @implements ArrayAccess<int|string, mixed>
 * @implements Iterator<int|string, mixed>
 */
class StoryblokData implements StoryblokDataInterface, Iterator, ArrayAccess, Countable
{
    use IterableDataTrait;

    /**
     * @param array<mixed> $data The initial data to store in the object.
     */
    public function __construct(protected array $data = []) {}

    /**
     * Factory method to create a new instance of StoryblokData.
     *
     * @param array<mixed> $data The data to initialize the object with.
     * @return StoryblokData A new instance of StoryblokData.
     */
    public static function make(array $data = []): StoryblokData
    {
        return new StoryblokData($data);
    }

    /**
     * Returns the internal data as an array.
     *
     * @return array<mixed> The underlying data in array form.
     */
    public function toArray(): array
    {
        return $this->data;
    }


    public function toJson(): string|false
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }


    /**
     * Retrieves a value from the data by key. Supports dot notation for nested keys.
     *
     * @param mixed $key The key to retrieve the value for. Can use dot notation for nested keys.
     * @param mixed|null $defaultValue The default value to return if the key does not exist.
     * @param string $charNestedKey The character used for separating nested keys (default: ".").
     * @param bool $raw Whether to return raw data or cast it into StoryblokData if applicable.
     * @return mixed The value associated with the key, or the default value if the key does not exist.
     */
    public function get(mixed $key, mixed $defaultValue = null, string $charNestedKey = ".", bool $raw = false): mixed
    {
        if (is_string($key)) {
            if ($charNestedKey === "") {
                $charNestedKey = ".";
            }

            $keyString = strval($key);
            if (str_contains($keyString, $charNestedKey)) {
                $nestedValue = $this->data;
                foreach (explode($charNestedKey, $keyString) as $nestedKey) {
                    if (is_array($nestedValue) && array_key_exists($nestedKey, $nestedValue)) {
                        $nestedValue = $nestedValue[$nestedKey];
                    } elseif ($nestedValue instanceof StoryblokData) {
                        $nestedValue = $nestedValue->get($nestedKey);
                    } else {
                        return $defaultValue;
                    }
                }

                return $this->returnData($nestedValue, $raw);
            }

            if (! array_key_exists($key, $this->data)) {
                return $defaultValue;
            }

        }


        return $this->returnData($this->data[$key], $raw) ?? $defaultValue;

    }

    public function getString(mixed $key, string $defaultValue = "", string $charNestedKey = "."): string
    {
        $returnValue = $this->get($key, "", $charNestedKey);

        if (is_scalar($returnValue)) {
            return strval($returnValue);
        }

        return $defaultValue;
    }

    public function getBoolean(mixed $key, bool $defaultValue = false, string $charNestedKey = "."): bool
    {
        $returnValue = $this->get($key, false, $charNestedKey);

        if (is_scalar($returnValue)) {
            return boolval($returnValue);
        }

        return $defaultValue;
    }

    public function getFormattedDateTime(
        mixed $key,
        string $defaultValue = "",
        string $charNestedKey = ".",
        string $format = "Y-m-d H:i:s",
    ): string|null {
        $value =  $this->getString($key, "", $charNestedKey);


        if ($value === "") {
            return "";
        }

        try {
            $date = new \DateTimeImmutable($value);
        } catch (\DateMalformedStringException) {
            return $defaultValue;
        }

        return $date->format($format);
    }


    /**
     * Sets a value for a specific key in the data.
     * Supports dot notation for nested keys.
     *
     * @param int|string $key The key to set the value for. Can use dot notation for nested keys.
     * @param mixed $value The value to set.
     * @param string $charNestedKey The character used for separating nested keys (default: ".").
     * @return $this The current instance for method chaining.
     */
    public function set(int|string $key, mixed $value, string $charNestedKey = "."): self
    {
        if (is_string($key)) {
            $array = &$this->data;
            if ($charNestedKey === "") {
                $charNestedKey = ".";
            }

            $keys = explode($charNestedKey, $key);
            foreach ($keys as $i => $key) {
                if (count($keys) === 1) {
                    break;
                }

                unset($keys[$i]);

                if (!isset($array[$key]) || !is_array($array[$key])) {
                    $array[$key] = [];
                }

                $array = &$array[$key];
            }

            $array[array_shift($keys)] = $value;
            return $this;
        }

        $this->data[$key] = $value;
        return $this;
    }


    /**
     * Returns data in the desired format.
     * Can be raw or converted to StoryblokData.
     *
     * @param mixed $value The value to process.
     * @param bool $raw Whether to return raw data or cast it into StoryblokData if applicable.
     * @return mixed The processed value.
     */
    protected function returnData(mixed $value, bool $raw = false): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (is_scalar($value)) {
            return $value;
        }

        if ($raw) {
            return $value;
        }

        if (is_array($value)) {
            return new StoryblokData($value);
        }



        if ($value instanceof StoryblokData) {
            return $value;
        }

        return new StoryblokData([]);

    }


    /**
     * Returns the class name of the current data class.
     * This is useful when you extend the StoryblokData
     * and you want to cast the items returned during
     * the iteration (loops like foreach)
     * @return string The fully qualified class name.
     */
    public function getDataClass(): string
    {
        return self::class;
    }


    /**
     * @deprecated
     * Retrieves a nested value from the data and casts it into StoryblokData if applicable.
     *
     * @param mixed $key The key to retrieve the value for. Can use dot notation for nested keys.
     * @param mixed|null $defaultValue The default value to return if the key does not exist.
     * @param string $charNestedKey The character used for separating nested keys (default: ".").
     * @return self|string|null The processed value as StoryblokData, string, or null.
     */
    /*
    public function getData(mixed $key, mixed $defaultValue = null, string $charNestedKey = "."): self|string|null
    {
        $value = $this->get($key, $defaultValue, $charNestedKey);
        return $this->returnData($value);

    }*/

    /**
     * Counts the number of top-level elements in the data.
     *
     * @return int The number of elements in the data.
     */
    public function count(): int
    {
        return count($this->data);
    }
}
