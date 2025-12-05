<?php

namespace Mrjokermr\LivewireMultiSelect\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

class MultiSelect extends Component
{
    public SelectSettings $settings;
    public array $options = [];
    #[Modelable]
    public mixed $selected = [];
    public ?array $singleSelectionValue = null;
    public ?string $selectedTranslationKey = null;
    public ?string $searchValue = null;
    public ?string $name = null;

    public function mount()
    {
        if ($this->selected === null) {
            if ($this->settings->isSingleValueMode()) {
                $this->selected = null;
            } else {
                $this->selected = [];
            }
        }

        if (
            $this->name === null && $this->attributes->get(1) !== null
            && $this->attributes->get(1)::class === 'Livewire\Attributes\Modelable'
        ) {
            $this->name = $this->modelableName();
        }

        $this->loadOptionsAndSetInitialValue();

        $this->selectedTranslationKey = config('multi-select.translations.selected');

        $this->selectedString();
    }


    public function modelableName(): ?string
    {
        foreach (\Livewire\store($this)->get('bindings') as $parentProp => $childProp) {
            if ($childProp === 'selected') {
                return $parentProp;
            }
        }

        return null;
    }

    private function loadOptionsAndSetInitialValue()
    {
        $initialValue = $this->settings->getInitialValue();

        if ($initialValue !== null) {
            if ($this->settings->isSingleValueMode()) {
                $this->singleSelectionValue = $initialValue;
                $this->setOptions();
            } else {
                $this->setOptions();
                $this->selected[] = $initialValue['key'];

                if (!array_key_exists($initialValue['key'], $this->options)) {
                    $this->options[$initialValue['key']] = $initialValue['value'];
                }
            }
        } else {
            $this->setOptions();
        }
    }


    public function updatedSearchValue(string $value)
    {
        $this->options = $this->settings->getOptions(searchValue: $value);
    }

    private function setOptions()
    {
        $this->options = $this->settings->getOptions(searchValue: $this->searchValue);
        if ($this->settings->isSingleValueMode() && $this->singleSelectionValue !== null) {
            $valueInArray = ($this->options[$this->singleSelectionValue['key']] ?? null) !== null;
            if (!$valueInArray) {
                $this->options[$this->singleSelectionValue['key']] = $this->singleSelectionValue['value'];
            }
        }
    }

    public function toggleSelect($value)
    {
        $value = (string) $value;

        if ($this->settings->isSingleValueMode()) {
            $this->selected = $value;

            $label = $this->options[$value]
                ?? $this->singleSelectionValue['value']
                ?? '';

            $this->singleSelectionValue = [
                'key' => $value,
                'value' => $label,
            ];
        } else {
            // normalize selected to strings
            $selected = array_map('strval', $this->selected ?? []);

            $key = array_search($value, $selected, true);

            if ($key === false) {
                $selected[] = $value;
            } else {
                unset($selected[$key]);
                $selected = array_values($selected);
            }

            $this->selected = $selected;
        }

        if ($this->settings->getCloseOnSelect() && !empty($this->searchValue)) {
            $this->searchValue = null;
            $this->setOptions();
        }

        $eventName = $this->settings->getEventName();
        if ($eventName !== null) {
            $this->dispatch($eventName, selection: $this->selected);
        }
    }


    #[Computed]
    public function selectedString(): string
    {
        if ($this->settings->isSingleValueMode()) {
            $value = $this->singleSelectionValue['value'] ?? '';
        } else {
            if (count($this->selected) === 0 && !empty($this->settings->getPlaceholder())) {
                return $this->settings->getPlaceholder();
            }

            $value = '';
            if ($this->settings->getSelectedText() !== null) {
                $value .= $this->settings->getSelectedText().' ';
            } elseif ($this->selectedTranslationKey) {
                $translationValue = __($this->selectedTranslationKey);
                if (trim($translationValue) === '' || $translationValue === null) {
                    $translationValue = 'Selected';
                }
                $value .= trim($translationValue).' ';
            } else {
                $value .= 'Selected ';
            }

            $value .= count($this->selected);
        }

        return $value;
    }

    public function render()
    {
        return view('livewire-multi-select::Livewire.multi-select');
    }
}
