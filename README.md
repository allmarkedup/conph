Conph
=====

A little configuration class for PHP 5.4+.

**Work In Progress.** You have been warned.

## Installation and use

Using composer:

```bash
$ composer require allmarkedup/conph
```

## Example

```php
<?php

class MyConfig extends Amu\Conph\Config
{
    public function convertPaths($value)
    {
        return array_map(function($item){
            return '/my/base/path' . $item;
        }, $value);
    }
}

$config = new MyConfig([
    "paths" => [
        "templates" => "the_template_path",
        "config" => "the_config_path"
    ],
    "settings" => [
        "debug" => true
    ],
    'names' => [
        "batman" => 'Fred',
        "superman" => 'Dave'
    ]
]);

// Nested values - dot notation
$debug = $config->get('settings.debug');
$config->set('names.batman', 'Clive');

// Custom converter functions
$config->get('paths.templates'); // => /my/base/path/the_template_path

```
