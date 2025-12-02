<?php
namespace Mrjokermr\LivewireMultiSelect\Classes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Livewire\Wireable;
use Mrjokermr\LivewireMultiSelect\Enums\MultiSelectType;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use \Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class SelectSettings implements Wireable
{
    public string $id;
    public MultiSelectType $type;
    public ?SelectEloquentSettings $multiSelectEloquentSettings = null;
    public ?array $options = null;
    public array $cssClasses;

    public ?string $label = null;
    private bool $closeOnSelect = true;
    private bool $withSearch = false;
    private ?string $eventName = null;

    public function __construct()
    {
        $this->id = uuid_create();
        $this->closeOnSelect = config('multi-select.close_on_select_default') ?? false;
        $this->cssClasses = config('multi-select.css_classes');
    }

    private static function create(?string $event = null): self
    {
        $instance = (new self());
        $instance->eventName = $event;

        return $instance;
    }

    /**
     * Create a Multi select settings instance
     *
     * The source:
     *  - An array of key/value pairs (['value' => 'Label'])
     *
     * @param array<string|int, string> $source
     * @return SelectSettings
     */
    public static function simple(array $source, ?string $event = null): self
    {
        $instance = self::create(event: $event)->setType(type: MultiSelectType::FIXED_OPTIONS);
        $instance->options = $source;

        return $instance;
    }

    /**
     * @param string $class
     * @param string $keyAttribute
     * @param string $labelAttribute
     * @param string|null $event
     * @param Builder|QueryBuilder|BuilderContract|null $baseQuery
     * @return SelectSettings
     * @throws Exception
     */
    public static function eloquentModel(
        string $class,
        string $keyAttribute,
        string $labelAttribute,
        ?string $event = null,
        int $limit = 20,
        Builder|QueryBuilder|BuilderContract $baseQuery = null,
    ): self {
        if ((new $class instanceof Model) === false) {
            $eloquent = Model::class;
            throw new Exception("Only $eloquent models allowed");
        }

        $instance = self::create(event: $event)->setType(type: MultiSelectType::ELOQUENT);

        $instance->multiSelectEloquentSettings = SelectEloquentSettings::make(
            class: $class,
            keyAttribute: $keyAttribute,
            labelAttribute: $labelAttribute,
            limit: $limit,
            baseQuery: $baseQuery,
        );

        return $instance;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function setCssClasses(
        ?string $input = null,
        ?string $inputSearch = null,
        ?string $label = null,
        ?string $listBox = null,
        ?string $listBoxOptionWrapper = null,
        ?string $listBoxOption = null,
        ?string $inputErrorLabel = null
    ): self {
        if ($input !== null) {
            $this->cssClasses['input'] = $input;
        }

        if ($inputSearch !== null) {
            $this->cssClasses['input_search'] = $inputSearch;
        }

        if ($label !== null) {
            $this->cssClasses['label'] = $label;
        }

        if ($listBox !== null) {
            $this->cssClasses['list_box'] = $listBox;
        }

        if ($listBoxOptionWrapper !== null) {
            $this->cssClasses['list_box_option_wrapper'] = $listBoxOptionWrapper;
        }

        if ($listBoxOption !== null) {
            $this->cssClasses['list_box_option'] = $listBoxOption;
        }

        if ($inputErrorLabel !== null) {
            $this->cssClasses['input_error_label'] = $inputErrorLabel;
        }

        return $this;
    }

    public function enableSearch(bool $value = true): self
    {
        $this->withSearch = $value;
        return $this;
    }

    public function showSearch(): bool
    {
        return $this->withSearch;
    }

    public function setCloseOnSelect(bool $value = true): self
    {
        $this->closeOnSelect = $value;
        return $this;
    }

    public function getCloseOnSelect(): bool
    {
        return $this->closeOnSelect;
    }

    public function getOptions(): array
    {
        if ($this->type === MultiSelectType::FIXED_OPTIONS) {
            return $this->options ?? [];
        } elseif ($this->type === MultiSelectType::ELOQUENT) {
            return $this->getOptionsViaEloquent();
        }

        return [];
    }

    private function getOptionsViaEloquent(): array
    {
        $settings = $this->multiSelectEloquentSettings;
        $baseQuery = $settings->getQueryBuilder();

        $optionsCollection = $baseQuery->select([$settings->keyAttribute, $settings->labelAttribute])->get();

        $options = [];
        foreach ($optionsCollection as $option) {
            $options[$option->{$settings->keyAttribute}] = $option->{$settings->labelAttribute};
        }

        return $options;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    private function setType(MultiSelectType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'type'    => $this->type->value,
            'options' => $this->options,
            'cssClasses' => $this->cssClasses,
            'label'    => $this->label,
            'withSearch' => $this->withSearch,
            'eventName' => $this->eventName,
            'closeOnSelect' => $this->closeOnSelect,
            'multiSelectEloquentSettings' => $this->multiSelectEloquentSettings?->toLivewire(),
        ];
    }

    public static function fromLivewire($value): self
    {
        $instance = self::create();

        $instance->setType(type: MultiSelectType::from($value['type']));
        $instance->id = $value['id'];
        $instance->options = $value['options'] ?? null;
        $instance->cssClasses = $value['cssClasses'] ?? config('multi-select.css_classes');
        $instance->label = $value['label'] ?? null;
        $instance->withSearch = $value['withSearch'];
        $instance->eventName = $value['eventName'] ?? null;
        $instance->closeOnSelect = $value['closeOnSelect'];
        $instance->multiSelectEloquentSettings = isset($value['multiSelectEloquentSettings']) ? SelectEloquentSettings::fromLivewire(value: $value['multiSelectEloquentSettings']) : null;

        return $instance;
    }
}
