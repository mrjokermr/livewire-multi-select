
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
        'input_error_label' => null,
        'label' => null,
        'list_box' => null,
        'list_box_option' => null,
    ],

    'translations' => [
        'selected' => null,
    ]
];
```
Make sure to set the translation key for the 'selected' value.



## Usage/Examples

Supply the multi select livewire component with the right settings:

```\Mrjokermr\LivewireMultiSelect\Classes\MultiSelectSettings::simple()```

Pass your options to this function as an associative array — the array key will be used as the option’s value, and the array value will be shown as the label.

```php
<livewire:multi-select
    wire:model="updateOrCreateUserForm.regionIds"
    :multiSelectSettings="
        \Mrjokermr\LivewireMultiSelect\Classes\MultiSelectSettings::simple(
            source: [1 => 'Example user', 2 => 'Second user'],
        )->setLabel('Users')->setCloseOnSelect(false)
    "
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
```php
->setCssClasses(
    input: 'input',
    label: 'label',
    listBox: 'list-box',
    listBoxOption: 'list-box-option',
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
