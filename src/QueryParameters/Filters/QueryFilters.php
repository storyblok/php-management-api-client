<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\QueryParameters\Filters;

class QueryFilters
{
    /**
     * @var array<Filter>
     */
    private array $array = [];

    public function add(Filter $filter): QueryFilters
    {
        $this->array[] = $filter;
        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        /** @var mixed[] $array */
        $array = [];

        foreach ($this->array as $filter) {
            if (! array_key_exists("filter_query", $array)) {
                $array["filter_query"] = [];
            }

            if (! array_key_exists($filter->field, $array["filter_query"])) {
                $array["filter_query"][$filter->field] = [];
            }

            $array["filter_query"][$filter->field][$filter->operator] = is_array($filter->value) ? implode(',', $filter->value) : $filter->value;

        }

        return $array;

    }



}
