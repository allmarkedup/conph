<?php namespace Amu\Conph;

use Amu\Dayglo\Loader;
use Amu\Dayglo\Writer;
use Amu\Dayglo\Parser;
use Amu\Dayglo\ParserCollection;

class Config
{
    protected $raw = [];

    protected $config = [];

    protected $loader;

    protected $writer;

    public function __construct(array $config = [], Loader $loader = null, Writer $writer = null)
    {
        $this->raw = $config;
        $this->loader = $loader;
        $this->writer = $writer;
        $this->applyConverters();
    }

    public static function createFromFile($path, Loader $loader = null)
    {
        if ( ! file_exists($path) ) {
            throw new \InvalidArgumentException('The file ' . $path . ' was not found');    
        }
        $self = new static([], $loader);
        $self->mergeWithFile($path);
        return $self;
    }

    public static function createFromArray(array $array = [], Loader $loader = null)
    {
        return new static($array, $loader);
    }

    public function merge($config = [])
    {
        $this->raw = Helper::merge($this->raw, $config);
        $this->applyConverters();
    }

    public function mergeWithFile($path)
    {
        if ( ! file_exists($path) ) {
            throw new \InvalidArgumentException('The file ' . $path . ' was not found');    
        }
        $loader = $this->getLoader();
        $this->merge((array) $loader->fetch($path)->getData());
    }

    public function get($path = null, $default = null)
    {
        if (is_null($path)) {
            return $this->config;
        }
        return Helper::get($this->config, $path, $default);
    }

    public function getRaw($path = null, $default = null)
    {
        if (is_null($path)) {
            return $this->raw;
        }
        return Helper::get($this->raw, $path, $default);
    }

    public function set($path, $value)
    {
        Helper::set($this->raw, $path, $value);
        $this->applyConverters();
    }

    public function write($path, $raw = true)
    {
        $data = $raw ? $this->raw : $this->config;
        $writer = $this->getWriter();
        $writer->setData($data);
        return $writer->write($path);
    }

    protected function applyConverters()
    {
        $methods = get_class_methods($this);
        $this->config = $this->raw;
        foreach ($methods as $method) {
            if (strpos($method, 'convert') === 0) {
                $path = $this->getPathFromConverterName($method);
                $rawValue = Helper::get($this->raw, $path);
                $convertedValue = $this->$method($rawValue);
                Helper::set($this->config, $path, $convertedValue);
            }
        }
    }

    protected function getPathFromConverterName($methodName)
    {
        $pathName = substr($methodName, 7);
        $parts = explode('_', $pathName);
        $parts = array_map(function($part){
            preg_match_all('/((?:^|[A-Z])[a-z]+)/', $part, $matches);
            $matches = $matches[0];
            return strtolower(implode('.', $matches));
        }, $parts);
        return implode('_', $parts);
    }

    protected function getLoader()
    {
        if ( is_null($this->loader) ) {
            $parsers = new ParserCollection([
                new Parser\JsonParser(),
                new Parser\YamlParser(),
                new Parser\PhpParser(),
                new Parser\CsvParser(),
                new Parser\TomlParser(),
            ]);
            $this->loader = new Loader($parsers);    
        }
        return $this->loader;
    }

    protected function getWriter()
    {
        if ( is_null($this->writer) ) {
            $parsers = new ParserCollection([
                new Parser\JsonParser(),
                new Parser\YamlParser(),
                new Parser\PhpParser(),
                new Parser\CsvParser(),
                new Parser\TomlParser(),
            ]);
            $this->writer = new Writer($parsers);    
        }
        return $this->writer;
    }

}