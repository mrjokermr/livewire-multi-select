<?php
namespace Mrjokermr\LivewireMultiSelect\Classes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Livewire\Wireable;
use Mrjokermr\LivewireMultiSelect\Enums\MultiSelectType;

class MultiSelectSettings implements Wireable
{
    public string $id;
    public MultiSelectType $type;
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
     * Create a Multi select instance with dynamic options.
     *
     * The source can be:
     *  - An array of key/value pairs (['value' => 'Label'])
     *  - An Eloquent model class name implementing a standard option query
     *
     * @param array<string|int, string>|class-string<Model>  $source
     * @return MultiSelectSettings
     * @throws Exception
     */
    public static function simple(array|string $source, ?string $event = null): self
    {
        if (is_array($source)) {
            $instance = self::create(event: $event)->setType(type: MultiSelectType::FIXED_OPTIONS);
            $instance->options = $source;

            return $instance;
        } elseif (is_string($source)) {
            return self::eloquentModel(class: $source);
        } else {
            throw new Exception("Invalid option source. Must be array, or Eloquent model class.");
        }
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $class
     * @return self
     * @throws Exception
     */
    private static function eloquentModel(string $class): self
    {
        if ((new $class instanceof Model) === false) {
            $eloquent = Model::class;
            throw new Exception("Only $eloquent models allowed");
        }

        $instance = self::create()->setType(type: MultiSelectType::ELOQUENT);
        $instance->class = $class;

        return $instance;
    }

    public function setCssClasses(?string $input = null, ?string $label = null, ?string $listBox = null, ?string $listBoxOption = null, ?string $inputErrorLabel = null): self
    {
        if ($input !== null) {
            $this->cssClasses['input'] = $input;
        }

        if ($label !== null) {
            $this->cssClasses['label'] = $label;
        }

        if ($listBox !== null) {
            $this->cssClasses['list_box'] = $listBox;
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

        return $instance;
    }
}
