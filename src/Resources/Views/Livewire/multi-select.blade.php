<div
    x-data="{
        open:false,
        query:'',
        menuWidth:0,
        setWidth(){ this.menuWidth = this.$refs.input?.offsetWidth || 0 },
        init(){ this.$watch('open', v => v && this.$nextTick(() => this.setWidth())) }
    }"
    style="position: relative; width: auto"
    @focusin="open=true"
    @focusout.window="if(!$el.contains($event.relatedTarget)) open=false"
    @keydown.escape.window="open=false"
>
    @if ($multiSelectSettings->label !== null)
        <label for="input_search_{{ $multiSelectSettings->id }}" class="{{ $multiSelectSettings->cssClasses['label'] }}">{{ $multiSelectSettings->label }}</label>
    @endif
    <input
        id="input_search_{{ $multiSelectSettings->id }}"
        name="input_search_{{ $multiSelectSettings->id }}"
        readonly
        x-ref="input"
        @focus="open=true; setWidth()"
        class="{{ $multiSelectSettings->cssClasses['input'] }}"
        value="{{ $this->selectedString }}"
        autocomplete="off"
        role="combobox"
        :aria-expanded="open"
        aria-controls="multi_select_options_{{ $multiSelectSettings->id }}"
    />

    <div
        id="multi_select_options_{{ $multiSelectSettings->id }}"
        x-data="{ base:@js($multiSelectSettings->cssClasses['list_box'] === null
        ? 'position:absolute; z-index:10; margin-top:0.25rem; max-height:15rem; overflow:auto; border-radius:0.375rem; border:1px solid rgb(229 231 235); background-color:#fff; box-shadow:0 1px 2px 0 rgb(0 0 0 / 0.05), 0 1px 3px 0 rgb(0 0 0 / 0.1)'
        : 'position:absolute') }"
        x-cloak
        x-show="open"
        x-transition
        @class([$multiSelectSettings->cssClasses['list_box']])
        :style="`${base}; min-width:${menuWidth}px`"
        role="listbox"
    >
        @if ($multiSelectSettings->showSearch())
            <input class="" wire:model.live.debounce.250ms="searchValue"/>
        @endif

        <ul>
            @foreach ($options as $value => $label)
                <li
                    wire:key="{{$multiSelectSettings->id}}_option_{{$value}}"
                    @if ($multiSelectSettings->getCloseOnSelect() === true)
                        wire:click="toggleSelect('{{ $value }}')"
                    @else
                        @mousedown.prevent.stop="$wire.toggleSelect('{{ $value }}'); open=true"
                    @endif
                    @if ($multiSelectSettings->cssClasses['list_box_option'] === null)
                        style="display:flex; width:100%; align-items:center; gap:0.5rem; padding:0.5rem 0.75rem; text-align:left; cursor:pointer;" onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor=''"
                    @else
                        class="{{ $multiSelectSettings->cssClasses['list_box_option'] }}"
                    @endif
                    role="option"
                >
                    @if (in_array($value, $this->selected))
                        <span>✓</span>
                    @endif
                    <span>{{ $label }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <style>[x-cloak]{display:none !important}</style>
</div>
