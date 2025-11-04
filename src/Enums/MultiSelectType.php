<?php
namespace Mrjokermr\LivewireMultiSelect\Enums;

enum MultiSelectType: string
{
    case ELOQUENT = 'eloquent';
    case FIXED_OPTIONS = 'fixed_options';
}
