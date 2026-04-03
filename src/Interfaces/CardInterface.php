<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Interfaces;

/**
 * CardInterface defines the contract for card entities.
 * It serves as a marker interface for classes that represent cards,
 * ensuring that they can be used interchangeably in contexts where a card is expected.
 */
interface CardInterface
{
    /**
     * Get the card number.
     * @return string The card number as a string.
     */
    public function getNumber(): string;

    /**
     * Check the Card number using the Luhn algorithm & check the first digit against the ISO/IEC 7812 prefixes.
     *
     * @return bool True if the credit card number is valid according to the Luhn algorithm,
     *      false otherwise.
     * @see CreditCardInterface::IEC_7812_PREFIXES
     *      for the expected industry categories based on the first digit of the card number.
     */
    public function isValidCardNumber(): bool;

    /**
     * Get the card brand associated with the card number.
     *
     * @param ?CreditCardInterface $creditCard
     * An optional credit card entity to guess the brand.
     *      If provided, the method may use the credit card's number and other properties to identify the brand.
     *      If null, the method may attempt to determine the brand based on the current card's number or other context.
     * @return CreditCardBrandInterface|null The card brand associated with the card number,
     *      or null if the brand cannot be determined.
     */
    public function getCardBrand(?CreditCardInterface $creditCard = null): ?CreditCardBrandInterface;
}
