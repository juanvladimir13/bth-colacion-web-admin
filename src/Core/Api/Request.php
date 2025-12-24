<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Core\Api;

class Request
{
    private array $body;
    private bool $error;

    public function __construct()
    {
        $this->error = true;
        $this->body = [];
        $json_string = \file_get_contents('php://input');
        if ($json_string !== false) {
            $json_data = \json_decode($json_string, true);
            if ($json_data !== false && $json_data !== null) {
                $this->body = $json_data;
                $this->error = false;
            }
        }
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getError(): bool
    {
        return $this->error;
    }

    public function existsColumns(array $keyColumns): bool
    {
        $count = 0;
        foreach ($keyColumns as $value) {
            if (array_key_exists($value, $this->body)) {
                $count++;
            }
        }

        return $count == count($keyColumns);
    }
}
