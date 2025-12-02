<?php

namespace Mrjokermr\LivewireMultiSelect\Classes;

use DB;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Wireable;

class SelectEloquentSettings implements Wireable
{
    public function __construct(
        private string $class,
        public string $keyAttribute,
        public string $labelAttribute,
        public int $limit,
        private ?string $query = null,
        private ?array $queryBindings = null,
    )
    {}

    public static function make(
        string $class,
        string $keyAttribute,
        string $labelAttribute,
        int $limit = 20,
        Builder|QueryBuilder|BuilderContract $baseQuery = null,
    ): self {
        return new self(
            class: $class,
            keyAttribute: $keyAttribute,
            labelAttribute: $labelAttribute,
            limit: $limit,
            query: $baseQuery?->toSql(),
            queryBindings: $baseQuery !== null ? $baseQuery->getBindings() : [],
        );
    }

    public function getQueryBuilder(?string $searchValue, null|string|array $extraSearchAttributes = null): QueryBuilder|BuilderContract
    {
        if ($this->query !== null) {
            $query = DB::table(DB::raw("({$this->query}) as sub"))->setBindings($this->queryBindings);
        } else {
            /** @var Model $class */
            $class = $this->class;

            $query = $class::query();
        }

        if (!empty($searchValue)) {
            if ($extraSearchAttributes !== null) {
                $extraSearchAttributes = is_string($extraSearchAttributes) ? [$extraSearchAttributes] : $extraSearchAttributes;
                $extraSearchAttributes[] = $this->labelAttribute;

                $query->where(function ($query) use ($searchValue, $extraSearchAttributes) {
                    foreach ($extraSearchAttributes as $attribute) {
                        $query->orWhere($attribute, 'like', "%{$searchValue}%");
                    }
                });
            } else {
                $query->where($this->labelAttribute, 'like', "%{$searchValue}%");
            }
        }

        return $query->limit($this->limit);
    }

    public function toLivewire(): array
    {
        return [
            'class' => $this->class,
            'keyAttribute' => $this->keyAttribute,
            'labelAttribute' => $this->labelAttribute,
            'limit' => $this->limit,
            'query' => $this->query,
            'queryBindings' => $this->queryBindings,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            class: $value['class'],
            keyAttribute: $value['keyAttribute'],
            labelAttribute: $value['labelAttribute'],
            limit: $value['limit'],
            query: $value['query'],
            queryBindings: $value['queryBindings'],
        );
    }

    public function __serialize(): array
    {
        return $this->toLivewire();
    }

    public function __unserialize(array $data): void
    {
        $instance = self::fromLivewire(value: $data);

        $this->class = $instance->class;
        $this->keyAttribute = $instance->keyAttribute;
        $this->labelAttribute = $instance->labelAttribute;
        $this->limit = $instance->limit;
        $this->query = $instance->query;
        $this->queryBindings = $instance->queryBindings;
    }
}
