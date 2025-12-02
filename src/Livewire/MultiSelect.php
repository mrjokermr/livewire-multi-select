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

    public function mount()
    {
        if ($this->selected === null) {
            if ($this->settings->isSingleValueMode()) {
                $this->selected = null;
            } else {
                $this->selected = [];
            }
        }

        $this->setOptions();
        $this->selectedTranslationKey = config('multi-select.translations.selected');

        $this->selectedString();
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
        if ($this->settings->isSingleValueMode()) {
            $this->selected = $value;
            $this->singleSelectionValue = [
                'key' => $value,
                'value' => $this->options[$value]
            ];
        } else {
            if (!in_array($value, $this->selected)) {
                $this->selected[] = $value;
            } else {
                $selected = $this->selected;
                unset($selected[array_search($value, $selected)]);
                $this->selected = $selected;
            }
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
            $value = '';
            if ($this->selectedTranslationKey) {
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
