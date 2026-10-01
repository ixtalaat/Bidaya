<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public static function validNumbers(): array
    {
        return [
            'local with trunk zero' => ['01001234567', '201001234567'],
            'with spaces and dashes' => ['0100 123-4567', '201001234567'],
            'with plus prefix' => ['+201001234567', '201001234567'],
            'with 0020 prefix' => ['00201001234567', '201001234567'],
            'already international' => ['201001234567', '201001234567'],
        ];
    }

    #[DataProvider('validNumbers')]
    public function test_normalizes_egyptian_numbers(string $input, string $expected): void
    {
        $this->assertSame($expected, PhoneNumber::normalize($input));
    }

    public function test_rejects_unusable_numbers(): void
    {
        $this->assertNull(PhoneNumber::normalize(null));
        $this->assertNull(PhoneNumber::normalize(''));
        $this->assertNull(PhoneNumber::normalize('12345'));
        $this->assertNull(PhoneNumber::normalize('0223456789'));
        $this->assertNull(PhoneNumber::normalize('not a number'));
    }
}
