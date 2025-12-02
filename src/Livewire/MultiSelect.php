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
    public ?array $selected = [];
    public ?string $selectedTranslationKey = null;
    public ?string $searchValue = null;

    public function mount()
    {
        if ($this->selected === null) {
            $this->selected = [];
        }

        $this->options = $this->settings->getOptions();
        $this->selectedTranslationKey = config('multi-select.translations.selected');

        $this->selectedString();
    }

    public function toggleSelect($value)
    {
        if (!in_array($value, $this->selected)) {
            $this->selected[] = $value;
        } else {
            $selected = $this->selected;
            unset($selected[array_search($value, $selected)]);
            $this->selected = $selected;
        }

        $eventName = $this->settings->getEventName();
        if ($eventName !== null) {
            $this->dispatch($eventName, selection: $this->selected);
        }
    }

    #[Computed]
    public function selectedString(): string
    {
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
        return $value;
    }

    public function render()
    {
        return view('livewire-multi-select::Livewire.multi-select');
    }
}
