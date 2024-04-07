<?php

namespace FpDbTest;

class Replacer
{
    public static function replace(string $specifier, mixed $value): string
    {
        return match ($specifier) {
            'd' => self::intVal($value),
            'f' => self::floatVal($value),
            'a' => self::arrayVal($value),
            '#' => self::idVal($value),
            default => self::val($value),
        };
    }

    private static function intVal(mixed $value): string
    {
        if (is_null($value)) {
            return self::nullVal();
        }

        if (is_int($value) || is_bool($value)) {
            return (string)(int)$value;
        }

        throw new \Exception('Bad value type');
    }

    private static function floatVal(mixed $value): string
    {
        if (is_null($value)) {
            return self::nullVal();
        }

        if (is_float($value)) {
            return (string)$value;
        }

        throw new \Exception('Bad value type');
    }

    private static function idVal(mixed $values): string
    {
        if (is_array($values)) {
            return '`' . implode('`, `', $values) . '`';
        }

        if (is_string($values)) {
            return "`$values`";
        }

        throw new \Exception('Bad value type');
    }

    private static function arrayVal(mixed $values): string
    {
        if (!is_array($values)) {
            throw new \Exception('Bad value type');
        }

        $result = [];
        foreach ($values as $key => $value) {
            $value = self::val($value);

            if (is_int($key)) {
                $result[] = $value;
            } else {
                $key = self::idVal($key);
                $result[] = "$key = $value";
            }
        }

        return implode(', ', $result);
    }

    private static function nullVal(): string
    {
        return 'NULL';
    }

    private static function val(mixed $value): string
    {
        if (is_null($value)) {
            return self::nullVal();
        }

        if (is_string($value)) {
            return self::stringVal($value);
        }

        if (is_int($value) || is_bool($value)) {
            return self::intVal((int)$value);
        }

        if (is_float($value)) {
            return self::floatVal($value);
        }

        throw new \Exception('Bad value type');
    }

    private static function stringVal(string $value): string
    {
        return "'$value'";
    }
}