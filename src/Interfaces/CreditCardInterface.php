<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Interfaces;

use Countable;

/**
 * CreditCardInterface defines the contract for credit card entities.
 * It serves as a marker interface for classes that represent credit cards,
 * ensuring that they adhere to a common brand and can be used interchangeably
 * in contexts where a credit card is expected.
 */
interface CreditCardInterface extends Countable
{
    /**
     * The minimum length of a Primary Account Number (PAN) for a credit card.
     * The PAN is the full card number, which includes the Issuer Identification Number (IIN/BIN)
     * and the individual account identifier. Valid PAN lengths should be between 8 and 19 digits, inclusive.
     * @see https://www.iso.org/standard/70484.html
     * @see https://en.wikipedia.org/wiki/Payment_card_number#Primary_account_number_(PAN)
     * @var int The minimum length of a PAN for a credit card.
     */
    public const MIN_PAN_LENGTH = 8;

    /**
     * The maximum length of a Primary Account Number (PAN) for a credit card.
     * The PAN is the full card number, which includes the Issuer Identification Number (IIN/BIN)
     * and the individual account identifier. Valid PAN lengths should be between 8 and 19 digits, inclusive.
     * @see https://www.iso.org/standard/70484.html
     * @see https://en.wikipedia.org/wiki/Payment_card_number#Primary_account_number_(PAN)
     * @var int The maximum length of a PAN for a credit card.
     */
    public const MAX_PAN_LENGTH = 19;

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) prefixes for credit cards.
     * @link https://en.wikipedia.org/wiki/ISO/IEC_7812
     */
    public const IEC_7812_PREFIXES = [
        // 0 => 'ISO/TC 68 and other industry assignments', // internal / special use
        1 => 'Airlines', // internal / special use
        2 => 'Airlines and other future industry assignments', // mastercard
        3 => 'Travel and entertainment, banking/financial',
        4 => 'Banking and financial', // visa
        5 => 'Banking and financial', // commonly mastercard
        6 => 'Merchandising and banking/financial', // discover
        //7 => 'Petroleum and other future industry assignments',
        //8 => 'Healthcare, telecommunications and other future industry assignments',
        //9 => 'National assignment' // internal / special use
    ];

    /**
     * Checks if the credit card entity has a specific type of credit card.
     *
     * @param string $brandId The brand of credit card to check for (e.g., "visa", "mastercard").
     * @return bool True if the credit card entity has the specified type, false otherwise.
     */
    public function has(string $brandId): bool;

    /**
     * Retrieves the CreditCardBrandInterface instance associated with the specified credit card type.
     *
     * @param string $brandId The brand identity of credit card to retrieve (e.g., "visa", "mastercard").
     * @return CreditCardBrandInterface The CreditCardBrandInterface instance associated with the specified type.
     * @throws \GatePay\CreditCard\Exceptions\NotFoundException
     *      If the specified credit card type is not found in the entity.
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function get(string $brandId): CreditCardBrandInterface;

    /**
     * Adds a CreditCardBrandInterface instance to the credit card entity.
     *
     * @param CreditCardBrandInterface $creditCardBrand The CreditCardBrandInterface instance to add.
     * @return bool True if the credit card brand was successfully added, false otherwise.
     */
    public function append(CreditCardBrandInterface $creditCardBrand): bool;

    /**
     * Adds a CreditCardBrandInterface instance to the beginning of the credit card entity.
     *
     * @param CreditCardBrandInterface $creditCardBrand The CreditCardBrandInterface instance to add.
     * @return bool True if the credit card brand was successfully added, false otherwise.
     */
    public function prepend(CreditCardBrandInterface $creditCardBrand): bool;

    /**
     * Replaces an existing CreditCardBrandInterface instance in the credit card entity with a new one.
     *
     * @param CreditCardBrandInterface $creditCardBrand The new CreditCardBrandInterface
     *      instance to replace the existing one.
     * @return ?CreditCardBrandInterface The previous CreditCardBrandInterface instance that was replaced,
     *      or null if there was no existing instance for the specified brand.
     */
    public function replace(CreditCardBrandInterface $creditCardBrand): ?CreditCardBrandInterface;

    /**
     * Removes the CreditCardBrandInterface instance associated with the specified credit card type from the entity.
     * Special case for remove that object as identifier checking
     *
     * @param string|CreditCardBrandInterface $brandId The brand of credit card to remove (e.g., "visa", "mastercard").
     * @return ?CreditCardBrandInterface The removed CreditCardBrandInterface instance if it was found and removed,
     *      or null if the specified credit card type was not found in the entity.
     */
    public function remove(string|CreditCardBrandInterface $brandId): ?CreditCardBrandInterface;

    /**
     * Get all credit card brands associated with this credit card entity.
     * The returned array is an associative array where the keys are lowercase strings representing
     * the credit card type identifiers (e.g., "visa", "mastercard") and the values are instances
     * of CreditCardBrandInterface that represent the corresponding credit card types.
     * @return array<non-empty-lowercase-string, CreditCardBrandInterface>
     *     An associative array where the keys are lowercase strings
     *     representing the credit card brand identifiers (e.g., "visa", "mastercard") and the values are
     */
    public function getBrands(): array;

    /**
     * Guesses the credit card brand based on the provided Primary Account Number (PAN).
     *
     * @param string|CardInterface $pan The Primary Account Number (PAN) to use for guessing the credit card brand.
     * @return CreditCardBrandInterface The CreditCardBrandInterface instance that matches the provided PAN.
     * @throws \GatePay\CreditCard\Exceptions\NotFoundException
     *     If no matching credit card brand is found for the provided PAN.
     * @throws \GatePay\CreditCard\Exceptions\ExceptionInterface
     *    If an error occurs during the guessing process (e.g., invalid PAN format).
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function guess(string|CardInterface $pan): CreditCardBrandInterface;

    /**
     * Masks a credit card number by replacing all but the first few and
     * last few digits with a specified masking character.
     * The number of visible digits at the start and end of the card number
     * is determined based on the total length of the card number.
     *
     * @param CardInterface|string $card
     * @param string $maskingCharacter
     * @return string
     */
    public static function mask(CardInterface|string $card, string $maskingCharacter = '*'): string;
}
