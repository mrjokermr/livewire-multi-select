
# Livewire multi select

**Simple Livewire 3 multi select box**

Uses native inline css styling for compatability.
## Installation

Install via composer:

```bash
composer require mrjokermr/livewire-multi-select
```

**Be sure to publish the configuration file so you can customize the CSS classes used to style this component.**
```bash
php artisan vendor:publish --tag=multi-select-config
```

## Config
```php
<?php
return [
    'close_on_select_default' => true,

    'css_classes' => [
        'input' => null,
        'input_search' => null, //when '$setting->enableSearch()' is applied
        'input_error_label' => null,
        'label' => null,
        'list_box' => null, //container
        'list_box_option_wrapper' => null, //ul
        'list_box_option' => null, //li
    ],

    'translations' => [
        'selected' => null,
    ]
];
```
Make sure to set the translation key for the 'selected' value.

## Usage/Examples

### Simple array
Supply the multi select livewire component with the right settings:

```\Mrjokermr\LivewireMultiSelect\Classes\SelectSettings::simple()```

Pass your options to this function as an associative array — the array key will be used as the option’s value, and the array value will be shown as the label.

```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

$settings = SelectSettings::simple(
    source: [1 => 'Example user', 2 => 'Second user'],
)->setLabel('Users')->setCloseOnSelect(false)

//In render file:
<livewire:multi-select
    wire:model.live="userIds"
    :settings="$settings"
></livewire:multi-select>
```

### Via eloquent models:
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

$settings = SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
    limit: 50, //Optional
)->setLabel('Users')

//In render file:
<livewire:multi-select
    wire:model.live="users"
    :settings="$settings"
></livewire:multi-select>
```

### Single value mode
Be aware that the value is an array.
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

$settings = SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
    limit: 50, //Optional
)->singleValueMode()

//In render file:
<livewire:multi-select
    wire:model.live="user"
    :settings="$settings"
></livewire:multi-select>
```

### Enable search for results
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
)->enableSearch()

//Or via SelectSettings::simple
SelectSettings::simple(
    source: [1 => 'Example user', 2 => 'Second user'],
)->enableSearch()
```


The ```labelAttribute``` attribute is always used for searching when having ```SelectSettings::eloquentModel()```, but you can add more attributes by passing ```searchAttributes:``` to the enableSearch() method.
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
)->enableSearch(searchAttributes: ['email'])
```

In the config you might set the styling for this search input element or use the setCssClasses() method
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
)->setCssClasses(
    inputSearch: 'input-search',
)
```

### Handle value changes via Livewire Events:
You might want to handle values changes via Livewire events, but it is not required since wire:model(.live) will also trigger value changes.
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

SelectSettings::simple(
    source: Chapter::all()->mapWithKeys(fn ($chapter) => [$chapter->id => $chapter->name])->toArray(),
    event: 'event_filter_chapters_updated',
)->setLabel('Chapters')->setCloseOnSelect(false);

//Event retrieval after each value change:
#[On('event_filter_chapters_updated')]
public function multiSelectValueChanged(array $selection)
{
    //implement code...
}
```

**You might also set the ```baseQuery``` for setting the base filter for the given options.**
```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

$settings = SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
    baseQuery: \App\Models\User::where('email', 'LIKE', '%example.com%')
)->setLabel('Users')

//In render file:
<livewire:multi-select
    wire:model.live="users"
    :settings="$settings"
></livewire:multi-select>
```

### Customization:

**Without label:**

```->setLabel()```

**Example:**
```php
->setLabel(__('regions.title'))
//or:
->setLabel('Label text')
```

**Overwrite config styling classes:**

```->setCssClasses()```

**Example:**
Each ```null``` value will default to config value.

```php
->setCssClasses(
    input: 'input',
    inputSearch: 'input-search',
    label: null,
    listBox: 'list-box',
    listBoxOptionWrapper: 'list-box-option-wrapper', //ul
    listBoxOption: 'list-box-option', //li
    inputErrorLabel: 'input-error-label',
)
```
This way you can style each individual multi select box

**Automatically close on select:**

```->setCloseOnSelect()```

**Example:**
```php
->setCloseOnSelect(false)
```

**You might change the default value in the config:**

```close_on_select_default```
