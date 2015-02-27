<?php namespace Amu\Conph;

class Helper
{
    public static function merge( array &$array1, array &$array2 )
    {
        $merged = $array1;
        foreach ( $array2 as $key => &$value ) {
            if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
                $merged [$key] = static::merge( $merged [$key], $value );
            } else {
                $merged [$key] = $value;
            }
        }
        return $merged;
    }

    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;
        if (isset($array[$key])) return $array[$key];
        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) or ! array_key_exists($segment, $array)) {
                return static::value($default);
            }
            $array = $array[$segment];
        }
        return $array;
    }

    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if ( ! isset($array[$key]) or ! is_array($array[$key])) {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
        return $array;
    }

    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}