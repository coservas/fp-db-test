<?php

namespace FpDbTest;

class Parser
{
    private const string SPECIFIER_PATTERN = '(\?(?<specifier>a|f|d|#)?)';
    private const string SKIP_BLOCK_PATTERN = '(?<skip>\{.*\})';

    public static function parse(string $query, array $args, int $skipValue): ?string
    {
        $argNumber = 0;

        $callback = static function (array $matches) use (&$argNumber, $args, $skipValue): string {
            if (!array_key_exists($argNumber, $args)) {
                throw new \Exception('Args array too small');
            }

            if ($skipBlock = $matches['skip'] ?? null) {
                if ($args[$argNumber] === $skipValue) {
                    $argNumber++;
                    return '';
                }

                return self::parse(
                    mb_substr($skipBlock, 1, strlen($skipBlock) - 2),
                    [$args[$argNumber++]],
                    $skipValue
                );
            }

            return Replacer::replace($matches['specifier'] ?? '', $args[$argNumber++]);
        };

        $result = preg_replace_callback(
            '/'.self::SPECIFIER_PATTERN.'|'.self::SKIP_BLOCK_PATTERN.'/',
            $callback,
            $query,
        );

        return $result !== null ? $result : throw new \Exception('Failed to parse query');
    }
}