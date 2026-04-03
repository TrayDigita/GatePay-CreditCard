<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Algorithms;

use GatePay\CreditCard\Exceptions\DataOverflowException;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use GatePay\CreditCard\Exceptions\InvalidRangeException;
use function is_int;
use function is_numeric;
use function str_contains;
use function str_starts_with;
use function strlen;
use function strpbrk;

/**
 * Luhn algorithm implementation for validating credit card numbers.
 * The Luhn algorithm, also known as the "modulus 10" or "mod 10" algorithm,
 * is a simple checksum formula used to validate various identification numbers, including credit card numbers.
 * It works by performing a series of calculations digits of the number and checking if the result is divisible by 10.
 * ISO/IEC 7812-1 defines the structure of the card number, and the Luhn algorithm is used to validate it.
 * @link https://datatracker.ietf.org/doc/html/rfc6920
 * @link https://en.wikipedia.org/wiki/Luhn_algorithm
 * @link https://www.iso.org/standard/70484.html
 */
class Luhn
{
    /**
     * Filter the input number to retain only positive digits (0-9).
     * This filter contain traceable exceptions for invalid input,
     * such as non-numeric characters, negative numbers, decimal points, and exponential notation.
     * @param string|int $number The input number to be processed, which can be a string or an integer.
     * @return numeric-string processed number string containing only positive-digits
     * @throws InvalidRangeException if the input contains invalid characters or is out of range.
     * @throws InvalidDataTypeException if the input is not a string or an integer.
     */
    public static function filterDigit(string|int $number): string
    {
        if (is_int($number)) {
            if ($number < 0) {
                throw new InvalidRangeException(
                    message: 'Input number must be a positive integer.'
                );
            }
            return (string)$number;
        }

        if (!is_numeric($number)) {
            throw new InvalidDataTypeException(
                expectedType: 'numeric-string',
                actualType: 'string',
                message: 'Input must be a number.'
            );
        }

        if (str_contains($number, '.')) {
            throw new InvalidDataTypeException(
                expectedType: 'numeric-string',
                actualType: 'numeric-float',
                message: 'Input contains a decimal point. Only whole digits are allowed.'
            );
        }

        if (strpbrk($number, 'Ee') !== false) {
            throw new InvalidDataTypeException(
                expectedType: 'numeric-string',
                actualType: 'numeric-exponential',
                message: 'Input contains exponential notation (E/e). Only raw digits are allowed.'
            );
        }

        if (str_starts_with($number, '-')) {
            throw new InvalidRangeException(
                message: 'Input number must be a positive number.'
            );
        }

        if (str_starts_with($number, '+')) {
            throw new InvalidDataTypeException(
                expectedType: 'numeric-string',
                actualType: 'numeric-string-with-sign',
                message: 'Input contains a positive sign (+). Only raw digits are allowed.'
            );
        }
        return $number;
    }

    /**
     * Calculate the Luhn checksum for a given number.
     * This method first filters the input number to ensure it contains only valid digits,
     * and then performs the Luhn algorithm calculations to compute the checksum.
     * @param string|int $number The input number for which to calculate the Luhn checksum,
     * which can be a string or an integer.
     * @return int The calculated Luhn checksum as an integer.
     * @throws InvalidRangeException if the input contains invalid characters or is out of range.
     * @throws InvalidDataTypeException if the input is not a string or an integer.
     */
    public static function calculateModulus(string|int $number) : int
    {
        $number = self::filterDigit($number);
        $sum = 0;
        $length = strlen($number);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int)$number[$length - 1 - $i];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10;
    }

    /**
     * Validate a number using the Luhn algorithm.
     *
     * @param string|int $number The input number to be validated, which can be a string or an integer.
     * @throws InvalidRangeException if the input contains invalid characters or is out of range.
     * @throws InvalidDataTypeException if the input is not a string or an integer.
     * @throws DataOverflowException the input is not 0 (zero) after modulus calculation,
     *  which indicates an invalid Luhn checksum.
     */
    public static function assert(string|int $number) : void
    {
        $modulus = self::calculateModulus($number);
        if ($modulus !== 0) {
            throw new DataOverflowException(
                limit: 0,
                message: 'Invalid Luhn checksum. The calculated modulus is ' . $modulus . ' instead of 0.'
            );
        }
    }
}
