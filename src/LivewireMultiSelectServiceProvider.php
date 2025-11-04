<?php
namespace Mrjokermr\LivewireMultiSelect;

use Illuminate\Support\ServiceProvider;
use Livewire;
use Mrjokermr\LivewireMultiSelect\Livewire\MultiSelect;

class LivewireMultiSelectServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'livewire-multi-select');
        Livewire::component('multi-select', MultiSelect::class);

        $this->publishes([
            __DIR__ . '/Config/multi-select.php' => config_path('multi-select.php'),
        ], 'multi-select-config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/multi-select.php', // adjust if your path differs
            'multi-select'
        );
    }
}
