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
    public function getNames($value)
    {
        return array_map(function($name){
            return strtoupper($name);
        }, $value);
    }

    public function setPathsTemplates($value)
    {
        $this->setConfigItem('paths.templates', '/my/base/path'. $value);
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

// Setter and getter functions
$names = $config->get('names'); // => ["batman" => 'FRED', "superman" => 'DAVE']
$config->set('paths.templates', '/tpl'); // paths.templates => '/my/base/path/tpl'

```
