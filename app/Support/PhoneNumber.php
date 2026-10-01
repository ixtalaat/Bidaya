<?php

namespace App\Support;

final class PhoneNumber
{
    /**
     * Normalize an Egyptian phone number to international digits, or null when unusable.
     */
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0020')) {
            $digits = substr($digits, 4);
        }

        if (! str_starts_with($digits, '20') || strlen($digits) !== 12) {
            if (str_starts_with($digits, '0')) {
                $digits = substr($digits, 1);
            }

            if (strlen($digits) === 10) {
                $digits = '20'.$digits;
            }
        }

        return preg_match('/^201\d{9}$/', $digits) === 1 ? $digits : null;
    }
}
