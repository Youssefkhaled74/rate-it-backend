<?php

namespace App\Support;

class PhoneNormalizer
{
    /**
     * Normalize phone numbers: trim, remove inner non-digits, keep leading + if present.
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $p = trim($phone);
        if ($p === '') {
            return $p;
        }

        $hasPlus = str_starts_with($p, '+');
        $digits = preg_replace('/[^0-9]/', '', $p);

        return $hasPlus ? ('+' . $digits) : $digits;
    }
}
