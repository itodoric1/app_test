<?php

declare(strict_types=1);

namespace App\Support;

class LegacyPassword
{
    public static function verify(string $plain, string $storedHash): bool
    {
        if (password_get_info($storedHash)['algo'] !== null) {
            return password_verify($plain, $storedHash);
        }

        return hash('sha512', $plain) === $storedHash;
    }

    public static function needsRehash(string $storedHash): bool
    {
        if (password_get_info($storedHash)['algo'] === null) {
            return true;
        }

        return password_needs_rehash($storedHash, PASSWORD_DEFAULT);
    }

    public static function make(string $plain): string
    {
        return password_hash($plain, PASSWORD_DEFAULT);
    }
}
