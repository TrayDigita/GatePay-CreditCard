<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Algorithms;

use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\Exceptions\DataOverflowException;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use GatePay\CreditCard\Exceptions\InvalidRangeException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

class LuhnTest extends TestCase
{
    public const MASTER_CARD_TEST_NUMBER = '5425233010103441';

    public const VISA_CARD_TEST_NUMBER = '4532015112830366';

    // filterDigit Tests

    /**
     * Test filterDigit with valid integer input
     */
    public function testFilterDigitWithValidInteger(): void
    {
        $result = Luhn::filterDigit(1234567890);
        $this->assertSame('1234567890', $result);
    }

    /**
     * Test filterDigit with valid numeric string input
     */
    #[test]
    public function testFilterDigitWithValidNumericString(): void
    {
        $result = Luhn::filterDigit('4532015112830366');
        $this->assertSame('4532015112830366', $result);
    }

    /**
     * Test filterDigit with zero
     */
    #[test]
    public function testFilterDigitWithZero(): void
    {
        $result = Luhn::filterDigit(0);
        $this->assertSame('0', $result);
    }

    /**
     * Test filterDigit with leading zeros
     */
    #[test]
    public function testFilterDigitWithLeadingZeros(): void
    {
        $result = Luhn::filterDigit('0123456789');
        $this->assertSame('0123456789', $result);
    }

    /**
     * Test filterDigit with positive sign prefix exception
     */
    #[test]
    public function testFilterDigitWithPositiveSignPrefixException(): void
    {

        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('+1234567890');
    }

    /**
     * Test filterDigit with positive sign prefix
     */
    #[test]
    public function testFilterDigitWithPositiveSignPrefix(): void
    {

        try {
            Luhn::filterDigit('+1234567890');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('numeric-string-with-sign', $e->actualType);
        }
    }

    /**
     * Test filterDigit rejects negative integer
     */
    #[test]
    public function testFilterDigitRejectsNegativeInteger(): void
    {
        $this->expectException(InvalidRangeException::class);
        Luhn::filterDigit(-1234567890);
    }

    /**
     * Test filterDigit rejects negative string with minus sign
     */
    #[test]
    public function testFilterDigitRejectsNegativeString(): void
    {
        $this->expectException(InvalidRangeException::class);
        Luhn::filterDigit('-1234567890');
    }

    /**
     * Test filterDigit rejects decimal number
     */
    #[test]
    public function testFilterDigitRejectsDecimalString(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('xFF');
    }

    /**
     * Test filterDigit rejects exponential notation with 'e'
     */
    #[test]
    public function testFilterDigitRejectsExponentialNotationLowercase(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('1e5');
    }

    /**
     * Test filterDigit rejects exponential notation with 'E'
     */
    #[test]
    public function testFilterDigitRejectsExponentialNotationUppercase(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('1E5');
    }

    /**
     * Test filterDigit rejects non-numeric string
     */
    #[test]
    public function testFilterDigitRejectsNonNumericString(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('abcd1234');
    }

    /**
     * Test filterDigit rejects string with special characters
     */
    #[test]
    public function testFilterDigitRejectsSpecialCharacters(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('1234-5678-9012-3456');
    }

    /**
     * Test filterDigit rejects string with spaces
     */
    #[test]
    public function testFilterDigitRejectsStringWithSpaces(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::filterDigit('1234 5678 9012 3456');
    }

    // calculateModulus Tests

    /**
     * Test calculateModulus with valid credit card number (known valid Luhn)
     */
    #[test]
    public function testCalculateModulusWithValidCreditCard(): void
    {
        // 4532015112830366 is a valid Visa test number
        $result = Luhn::calculateModulus(self::VISA_CARD_TEST_NUMBER);
        $this->assertSame(0, $result);
    }

    /**
     * Test calculateModulus with another valid credit card number
     */
    #[test]
    public function testCalculateModulusWithAnotherValidCard(): void
    {
        $result = Luhn::calculateModulus(self::MASTER_CARD_TEST_NUMBER);
        $this->assertSame(0, $result);
    }

    /**
     * Test calculateModulus with integer input
     */
    #[test]
    public function testCalculateModulusWithIntegerInput(): void
    {
        $result = Luhn::calculateModulus(4532015112830366);
        $this->assertSame(0, $result);
    }

    /**
     * Test calculateModulus with single digit
     */
    #[test]
    public function testCalculateModulusWithSingleDigit(): void
    {
        $result = Luhn::calculateModulus('0');
        $this->assertSame(0, $result);
    }

    /**
     * Test calculateModulus with two digits
     */
    #[test]
    public function testCalculateModulusWithTwoDigits(): void
    {
        $result = Luhn::calculateModulus('18');
        $this->assertSame(0, $result);
    }

    /**
     * Test calculateModulus with invalid number
     */
    #[test]
    public function testCalculateModulusWithInvalidNumber(): void
    {
        $result = Luhn::calculateModulus('1234567890');
        $this->assertNotSame(0, $result);
        $this->assertGreaterThan(0, $result);
        $this->assertLessThan(10, $result);
    }

    /**
     * Test calculateModulus rejects negative number
     */
    #[test]
    public function testCalculateModulusRejectsNegativeNumber(): void
    {
        $this->expectException(InvalidRangeException::class);
        Luhn::calculateModulus(-123);
    }

    /**
     * Test calculateModulus rejects decimal
     */
    #[test]
    public function testCalculateModulusRejectsDecimal(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::calculateModulus('123.45');
    }

    /**
     * Test calculateModulus returns integer between 0 and 9
     */
    #[test]
    public function testCalculateModulusReturnsValidRange(): void
    {
        $result = Luhn::calculateModulus('4532015112830360');
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThan(10, $result);
    }

    // Assert Test

    /**
     * Test assert with valid Visa card number
     */
    #[test]
    public function testAssertWithValidVisaCardNumber(): void
    {
        // Should not throw exception
        Luhn::assert('4532015112830366');
        $this->addToAssertionCount(1);
    }

    /**
     * Test assert with valid Mastercard number
     */
    #[test]
    public function testAssertWithValidMastercardNumber(): void
    {
        // Should not throw exception
        Luhn::assert('5425233010103441');
        $this->addToAssertionCount(1);
    }

    /**
     * Test assert with valid American Express number
     */
    #[test]
    public function testAssertWithValidAmexNumber(): void
    {
        // 374245455400126 is a valid Amex test number
        Luhn::assert('374245455400126');
        $this->addToAssertionCount(1);
    }

    /**
     * Test assert with valid integer input
     */
    #[test]
    public function testAssertWithValidIntegerInput(): void
    {
        Luhn::assert(4532015112830366);
        $this->addToAssertionCount(1);
    }

    /**
     * Test assert with single valid digit
     */
    #[test]
    public function testAssertWithSingleValidDigit(): void
    {
        Luhn::assert('0');
        $this->addToAssertionCount(1);
    }

    /**
     * Test assert throws exception for invalid card number
     */
    #[test]
    public function testAssertThrowsExceptionForInvalidCardNumber(): void
    {
        $this->expectException(DataOverflowException::class);
        $this->expectExceptionMessage('Invalid Luhn checksum');
        Luhn::assert('4532015112830360');
    }

    /**
     * Test assert throws exception for another invalid card number
     */
    #[test]
    public function testAssertThrowsExceptionForAnotherInvalidCard(): void
    {
        $this->expectException(DataOverflowException::class);
        Luhn::assert('1234567890123456');
    }

    /**
     * Test assert throws exception for random invalid digits
     */
    #[test]
    public function testAssertThrowsExceptionForRandomInvalidDigits(): void
    {
        $this->expectException(DataOverflowException::class);
        Luhn::assert('9999999999999999');
    }

    /**
     * Test assert rejects negative number
     */
    #[test]
    public function testAssertRejectsNegativeNumber(): void
    {
        $this->expectException(InvalidRangeException::class);
        Luhn::assert(-4532015112830366);
    }

    /**
     * Test assert rejects decimal
     */
    #[test]
    public function testAssertRejectsDecimal(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::assert('453.2015112830366');
    }

    /**
     * Test assert rejects non-numeric string
     */
    #[test]
    public function testAssertRejectsNonNumericString(): void
    {
        $this->expectException(InvalidDataTypeException::class);
        Luhn::assert('abcd1234567890');
    }

    /**
     * Test assert exception contains expected modulus value
     */
    #[test]
    public function testAssertExceptionContainsModulusValue(): void
    {
        try {
            Luhn::assert('4532015112830360');
        } catch (DataOverflowException $e) {
            $this->assertStringContainsString('Invalid Luhn checksum', $e->getMessage());
            $this->assertSame(0, $e->limit);
        }
    }

    /**
     * Test assert with leading zeros in valid number
     */
    #[test]
    public function testAssertWithLeadingZeros(): void
    {
        Luhn::assert('0000000000000000');
        $this->addToAssertionCount(1);
    }
}
