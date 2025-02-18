<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Data;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * AbstractClass BaseData
 * Represents a wrapper for handling and manipulating
 * structured data.
 * Implements Iterator, ArrayAccess, and Countable
 * for seamless data traversal, access, and manipulation.
 * It provides the `get()` and the `set()` methods
 * for handling nested attributes/values.
 *
 * @implements ArrayAccess<int|string, mixed>
 * @implements Iterator<int|string, mixed>
 */
abstract class BaseData implements StoryblokDataInterface, Iterator, ArrayAccess, Countable
{
    use IterableDataTrait;

    /**
     * @var mixed[] $data
     */
    protected array $data = [];

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Set the internal data from an array
     *
     * @param mixed[] $data The underlying data in array form.
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getInt(mixed $key, int|null $defaultValue = null, string $charNestedKey = "."): int|null
    {
        $returnValue = $this->get($key, null, $charNestedKey);

        if (is_scalar($returnValue)) {
            return intval($returnValue);
        }

        return $defaultValue;
    }

    public function getBoolean(mixed $key, bool $defaultValue = false, string $charNestedKey = "."): bool
    {
        $returnValue = $this->get($key, $defaultValue, $charNestedKey);

        if (is_scalar($returnValue)) {
            return boolval($returnValue);
        }

        return $defaultValue;
    }

    /**
     * @param mixed[] $defaultValue
     * @return mixed[]
     */
    public function getArray(mixed $key, array $defaultValue = [], string $charNestedKey = "."): array
    {
        $returnValue = $this->get($key, $defaultValue, $charNestedKey);

        if (is_scalar($returnValue)) {
            return [strval($returnValue) ];
        }

        if ($returnValue instanceof StoryblokData) {
            return $returnValue->toArray();
        }

        return $defaultValue;
    }

    public function getFormattedDateTime(
        mixed $key,
        string $defaultValue = "",
        string $charNestedKey = ".",
        string $format = "Y-m-d H:i:s",
    ): string|null {
        $value = $this->getString($key, "", $charNestedKey);

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
        return StoryblokData::class;
    }

    /**
     * Counts the number of top-level elements in the data.
     *
     * @return int The number of elements in the data.
     */
    public function count(): int
    {
        return count($this->data);
    }

    public function has(mixed $value): bool
    {
        return in_array($value, $this->values()->toArray());
    }

    public function hasKey(string|int $key): bool
    {
        /** @var array<int, int|string> $keys */
        $keys = $this->keys();
        return in_array($key, $keys);
    }

    /**
     * @return StoryblokData object that contains the key/value pairs for each index in the array
     */
    public function values(): self
    {
        $pairs = $this->data;
        return StoryblokData::make($pairs);
    }

    /**
     * Returns a new array [] or a new StoryblokData object that contains the keys
     * for each index in the Block object
     * It returns StoryblokData or [] depending on $returnArrClass value
     *
     * @param bool $returnBlockClass true if you need StoryblokData object
     * @return int|string|array<int|string, mixed>|self
     */
    public function keys(bool $returnBlockClass = false): int|string|array|self
    {
        if ($returnBlockClass) {
            return StoryblokData::make(array_keys($this->data));
        }

        return array_keys($this->data);
    }

    public function toJson(): string|false
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    public function dump(): void
    {
        echo $this->toJson();
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
}
