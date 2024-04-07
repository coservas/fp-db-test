<?php

namespace FpDbTest;

class Parser
{
    private const string SPECIFIER_PATTERN = '(\?(?<specifier>a|f|d|#)?)';
    private const string SKIP_BLOCK_PATTERN = '(?<skip>\{.*\})';

    public static function parse(string $query, array $args, string $skipValue): ?string
    {
        $argNumber = 0;

        $callback = static function (array $matches) use (&$argNumber, $args, $skipValue): string {
            if (!array_key_exists($argNumber, $args)) {
                throw new \Exception('Args is too few');
            }

            if ($skipBlock = $matches['skip'] ?? null) {
                $value = $args[$argNumber];
                if ($value === $skipValue) {
                    $argNumber++;
                    return '';
                }

                $result = self::parse(
                    mb_substr($skipBlock, 1, strlen($skipBlock) - 2),
                    [$value],
                    $skipValue
                );

                if ($result === '') {
                    throw new \Exception('SkipBlock is not have question symbol');
                }

                $argNumber++;
                return $result;
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