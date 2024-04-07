<?php

namespace FpDbTest;

class Database implements DatabaseInterface
{
    private string $skipValue;

    public function __construct(private readonly ?\mysqli $mysqli = null)
    {
        $this->skipValue = microtime();
    }

    public function buildQuery(string $query, array $args = []): string
    {
        return Parser::parse($query, $args, $this->skipValue);
    }

    public function skip(): string
    {
        return $this->skipValue;
    }
}
