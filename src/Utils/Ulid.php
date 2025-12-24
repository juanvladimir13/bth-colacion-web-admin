<?php

/**
 * @author Juan Vladimir <juanvladimir13@gmail.com>
 * @link https://github.com/juanvladimir13
 */

declare(strict_types=1);

namespace BTH\Utils;

class Ulid
{
    public static function generate(int $length = 8, bool $split = false): string
    {
        $size = $split ? ($length / 2) : $length;
        $timestamp = (int)(microtime(true) * 1000);
        $timestampChars = self::encodeTime($timestamp, $size);

        $randomChars = '';
        for ($i = 0; $i < $size; $i++) {
            $randomChars .= self::encodeRandom(random_int(0, 31));
        }

        return $timestampChars . $randomChars;
    }

    private static function encodeTime(int $time, int $length): string
    {
        $chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        $encoded = '';

        for ($i = $length - 1; $i >= 0; $i--) {
            $encoded = $chars[$time % 32] . $encoded;
            $time = (int)($time / 32);
        }

        return $encoded;
    }

    private static function encodeRandom(int $value): string
    {
        $chars = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        return $chars[$value];
    }
}
