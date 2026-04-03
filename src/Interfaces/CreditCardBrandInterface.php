<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Interfaces;

use GatePay\CreditCard\CardType;
use Stringable;

/**
 * CreditCard Brand defines the contract for credit card type / provider entities.
 * It specifies the methods that must be implemented by any class that represents
 * a specific type of credit card, such as Visa, MasterCard, or American Express.
 *
 * @link https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
interface CreditCardBrandInterface extends Stringable
{
    /**
     * Get a unique identifier for the credit card type.
     * eg: visa, maestro, jcb.
     *
     * The identifier should be a string that uniquely distinguishes this credit card type from others.
     * Lowercase letters are commonly used for identifiers.
     *
     * @return non-empty-lowercase-string The unique identifier for the credit card type with lowercase letters.
     * @immutable
     */
    public function getId(): string;

    /**
     * Get the name of the credit card brand (e.g., "Visa", "MasterCard", "American Express").
     *
     * The name should be a human-readable string that describes the credit card type.
     * The name can include uppercase letters, spaces, and other characters as needed for clarity.
     *
     * @return non-empty-string The name of the credit card type.
     * @immutable
     */
    public function getName(): string;

    /**
     * Get the Issuer Identification Number (IIN/BIN) lists of the credit/debit card type.
     * The IIN/BIN is the first 6 digits of a credit/debit card number,
     * which identifies the issuing bank or institution.
     *
     * @note Following ISO/IEC 7812-1:2017 PAN structure and IIN/BIN assignment guidelines,
     *      the IIN/BIN ranges should be defined according to the standards set by
     *      the International Organization for Standardization (ISO), from 8 to 19 digits in length.
     *
     * @link https://www.iso.org/standard/70484.html
     * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     * @return non-empty-list<positive-int>
     *     An array of valid IIN/BIN ranges for this credit card type. Each range can be represented
     *     as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard) or as a positive integer
     *     (e.g., 4 for Visa, 51 for MasterCard).
     * @immutable
     */
    public function getIINList(): array;

    /**
     * Get the valid Primary Account Number (PAN) lengths for this credit card type.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     *
     * @note Following ISO/IEC 7812-1:2017 PAN structure guidelines,
     *      the PAN should be between 8 and 19 digits in length, inclusive.
     *
     * @link https://www.iso.org/standard/70484.html
     * @return non-empty-list<int<8,19>>
     *     An array of valid PAN lengths for this credit card type. Each length should be a positive integer
     *     representing the total number of digits in the card number (e.g., 16 for Visa, 15 for American Express).
     * @immutable
     */
    public function getPANLengths(): array;

    /**
     * Get the valid CVV (Card Verification Value) length for this credit card type.
     * The CVV is a security feature for credit card transactions, typically a 3 or 4-digit code.
     *
     * @return non-empty-list<int<3,4>> The valid CVV length for this credit card type
     * (e.g., 3 for Visa and MasterCard, 4 for American Express).
     * @immutable
     */
    public function getCVVLengths(): array;

    /**
     * Get the CardType instance associated with this credit card brand.
     *
     * @return CardType The CardType instance that represents this credit card brand.
     * @note This only reference, maybe getting false positives,
     *  as some brands can be both credit and debit cards, but for the sake of simplicity,
     *  we will assume that all brands are credit cards unless specified otherwise in the subclass.
     * @immutable
     */
    public function getType(): CardType;

    /**
     * Validate if the given PAN (Primary Account Number) is valid for this credit card type.
     *
     * This method should check if the PAN starts with a valid IIN/BIN from the getIINList()
     * and if the length of the PAN matches one of the valid lengths from getPANLengths().
     *
     * @param string $pan The Primary Account Number to validate.
     * @return bool True if the PAN is valid for this credit card type, false otherwise.
     */
    public function isValid(string $pan): bool;

    /**
     * Generate a valid PAN (Primary Account Number) for this credit card type.
     *
     * This method should create a random PAN that starts with a valid IIN/BIN from the getIINList()
     * and has a length that matches one of the valid lengths from getPANLengths().
     * The generated PAN should also pass the Luhn algorithm check,
     * which is commonly used for validating credit card numbers.
     * @param int|null $possibleNearestLength An optional parameter that suggests
     *      a preferred length for the generated PAN.
     *
     * @return numeric-string A valid PAN for this credit card type.
     */
    public function generate(?int $possibleNearestLength = null): string;

    /**
     * Generate CVV (Card Verification Value) for this credit card type.
     * @return numeric-string
     */
    public function generateCVV(): string;
}
