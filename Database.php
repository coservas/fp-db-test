<?php

namespace FpDbTest;

class Database implements DatabaseInterface
{
    private int $skipValue;

    public function __construct()
    {
        $this->skipValue = time();
    }

    public function buildQuery(string $query, array $args = []): string
    {
        return Parser::parse($query, $args, $this->skipValue);
    }

    public function skip(): int
    {
        return $this->skipValue;
    }
}
