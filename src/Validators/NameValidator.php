<?php namespace Greenf\Quasar\Validators;

class NameValidator {

    public static function isCamelCase(string $name)
    {
        return preg_match('/^[a-z]+(?:[A-Z][a-z]+)*$/', $name);
    }

    public static function isPascalCase(string $name)
    {
        return preg_match('/^[A-Z][a-z]+(?:[A-Z][a-z]+)*$/', $name);
    }

}