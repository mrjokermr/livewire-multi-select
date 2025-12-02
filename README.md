# Livewire Multi Select
A simple, yet powerful, multi-select component for Livewire 3. It uses native inline CSS for out-of-the-box compatibility but is fully customizable with your own CSS classes.

## Installation

Install the package via composer:

```bash
composer require mrjokermr/livewire-multi-select
```

Publish the configuration file to customize the component's appearance and behavior. This is a recommended step.

```bash
php artisan vendor:publish --tag=multi-select-config
```

This will create a `config/multi-select.php` file in your application.

## Configuration

The published `config/multi-select.php` file allows you to set default behaviors and styling.

```php
<?php
return [
    'close_on_select_default' => true,
    'display_input_error_default' => true,

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
- **`close_on_select_default`**: Determines if the dropdown should close automatically after selecting an option.
- **`display_input_error_default`**: Determines if by default input error messages should be displayed.
- **`css_classes`**: Define the CSS classes for each part of the component to match your project's design system (e.g., Tailwind CSS, Bootstrap).
- **`translations`**: Set the translation key for the "Selected" text displayed on the input. For example, you can set it to a key from your Laravel translation files.

## Quick nav

- [Usage & Examples](#usage--examples)
    - [Simple Array](#simple-array)
    - [Eloquent Models](#eloquent-models)
    - [Single Value Mode](#single-value-mode)
    - [Search Functionality](#search-functionality)
    - [Livewire Events](#livewire-events)
    - [Pre-filtering with Base Query](#pre-filtering-with-base-query)
    - [Usage with static forms (Without wire:model) & Error message handling](#usage-with-static-forms-without-wiremodel--error-message-handling)
- [Customization](#customization)
    - [Customizing the Label](#customizing-the-label)
    - [Setting the Placeholder](#setting-the-placeholder)
    - [Enable/Disable input error display](#enabledisable-input-error-display)
    - [Overriding CSS Classes](#overriding-css-classes)
    - [Close on Select Behavior](#close-on-select-behavior)

## Usage & Examples

### Simple Array

You can populate the select component with a simple associative array. The array key will be the option's value, and the array value will be the display label.

Use `\Mrjokermr\LivewireMultiSelect\Classes\SelectSettings::simple()` to create the settings.

```php
// In your Livewire Component
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

public function getSettings()
{
    return SelectSettings::simple(
        source: [1 => 'Example user', 2 => 'Second user'],
    )->setLabel('Users')->setCloseOnSelect(false);
}

// In your Blade view
<livewire:multi-select
    wire:model.live="userIds"
    :settings="$this->getSettings()"
/>
```

### Eloquent Models

Populate the options directly from an Eloquent model.

Use `\Mrjokermr\LivewireMultiSelect\Classes\SelectSettings::eloquentModel()` to define the source model and attributes.

```php
// In your Livewire Component
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;
use App\Models\User;

public function getSettings()
{
    return SelectSettings::eloquentModel(
        class: User::class,
        keyAttribute: 'id',
        labelAttribute: 'name',
        limit: 50, // Optional
    )->setLabel('Users');
}

// In your Blade view
<livewire:multi-select
    wire:model.live="users"
    :settings="$this->getSettings()"
/>
```

### Single Value Mode

To allow only a single selection, chain the `->singleValueMode()` method. The `wire:model` property will hold a single value instead of an `array`.

```php
// In your Livewire Component
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;
use App\Models\User;

public function getSettings()
{
    return SelectSettings::eloquentModel(
        class: User::class,
        keyAttribute: 'id',
        labelAttribute: 'name',
    )->singleValueMode();
}

// In your Blade view
<livewire:multi-select
    wire:model.live="user"
    :settings="$this->getSettings()"
/>
```

### Search Functionality

Enable a search input within the dropdown to filter options by chaining the `->enableSearch()` method.

```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

// For Eloquent models
SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
)->enableSearch();

// For simple arrays
SelectSettings::simple(
    source: [1 => 'Example user', 2 => 'Second user'],
)->enableSearch();
```

For Eloquent sources, you can specify additional attributes to search against.

```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;

SelectSettings::eloquentModel(
    class: \App\Models\User::class,
    keyAttribute: 'id',
    labelAttribute: 'name',
)->enableSearch(attributes: ['email']); // Searches 'name' and 'email'
```

In the config you might set the styling for search input element or use the ```setCssClasses(inputSearch: '.class-name')``` method
```php
->setCssClasses(
    inputSearch: 'input-search',
)
```

### Livewire Events

To handle value changes with Livewire events instead of `wire:model`, you can specify an event name. This is useful for triggering actions in parent components.

```php
// In your Livewire Component
use Livewire\Attributes\On;
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;
use App\Models\Chapter;

public function getSettings()
{
    return SelectSettings::simple(
        source: Chapter::all()->pluck('name', 'id')->toArray(),
        event: 'chapters_updated',
    )->setLabel('Chapters');
}

// Listen for the event
#[On('chapters_updated')]
public function handleChapterSelection(array $selection)
{
    // $selection contains the array of selected chapter IDs
}
```

### Pre-filtering with Base Query

When using `eloquentModel`, you can provide a `baseQuery` to apply a permanent filter to the available options.

```php
use Mrjokermr\LivewireMultiSelect\Classes\SelectSettings;
use App\Models\User;

public function getSettings()
{
    return SelectSettings::eloquentModel(
        class: User::class,
        keyAttribute: 'id',
        labelAttribute: 'name',
        baseQuery: User::where('email', 'LIKE', '%example.com%')
    )->setLabel('Users');
}

// In your Blade view
<livewire:multi-select
    wire:model.live="users"
    :settings="$this->getSettings()"
/>
```

### Usage with static forms (Without wire:model) & Error message handling
When setting the `name=`, the input element will contain the input name. And also errors will be displayed.
```php
// In your Blade view
<livewire:multi-select
    name="name"
    :settings="
        \Mrjokermr\LivewireMultiSelect\Classes\SelectSettings::eloquentModel(
            class: \App\Models\CuttingName::class,
            keyAttribute: 'id',
            labelAttribute: 'name',
        )
        ->setInitialValue(value: $cuttingName->id, label: $cuttingName->name)
        ->setCssClasses(inputErrorLabel: 'label-error')
    "
/>
```

## Customization

You can customize each instance of the component using fluent methods.

### Customizing the Label

Set a display label for the input. It can be omitted by not calling the method.
```php
->setLabel('Regions')
```
```php
->setLabel(__('regions.title'))
```

### Setting the Placeholder
```php
->setPlaceholder('Select users')
```

### Enable/Disable input error display
You can configure this value in the config file but you might also change the behavior via:

```php
->setDisplayError(value: true);
```

### Overriding CSS Classes

Override the default or configured CSS classes for a specific component instance. Any `null` values will fall back to the classes defined in `config/multi-select.php`.

```php
->setCssClasses(
    input: '.class',
    inputSearch: '.class',
    label: '.class',
    listBox: '.class',
    listBoxOptionWrapper: '.class', //ul
    listBoxOption: '.class', //li
    inputErrorLabel: '.class',
)
```

### Close on Select Behavior

Control whether the dropdown closes after an option is selected. The default is configured in `config/multi-select.php`.

```php
->setCloseOnSelect(false) // Keep the dropdown open after selection
