<?php
declare(strict_types=1);

namespace GatePay\CreditCard;

use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use GatePay\CreditCard\Interfaces\CardInterface;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use GatePay\CreditCard\Interfaces\CreditCardInterface;
use DateTimeInterface;
use Stringable;
use Throwable;
use function count;
use function date;
use function explode;
use function str_pad;
use function strlen;
use function substr;
use function trim;
use const STR_PAD_LEFT;

/**
 * The Card class represents a credit card with its essential details.
 * It implements the CardInterface and Stringable interfaces,
 * allowing it to be used in contexts where a card is expected
 * and to provide a string representation of the card (typically a masked version of the card number).
 */
class Card implements CardInterface, Stringable
{
    /**
     * @var ?non-empty-string $month The expiration month of the credit card,
     * represented as an integer (1-12).
     */
    private ?string $month = null;

    /**
     * @var ?non-empty-string $year The expiration year of the credit card,
     * represented as a four-digit integer (e.g., 2025).
     */
    private ?string $year = null;

    /**
     * @var ?string $cardholderName The name of the cardholder as it appears on the credit card.
     * This is an optional property that can be null if not provided.
     */
    private ?string $cardholderName = null;

    /**
     * @var ?string $cvv The Card Verification Value (CVV) code,
     * which is typically a 3 or 4-digit number found on the back of the card.
     * This is an optional property that can be null if not provided.
     */
    private ?string $cvv = null;

    /**
     * @var bool $validCardNumber A flag indicating whether the card number is valid based on the Luhn algorithm.
     * This is an optional property that can be null if not provided.
     */
    private bool $validCardNumber;

    /**
     * @var CreditCardBrandInterface|false|null $cardBrandFactory The brand of the credit card,
     * which can be determined based on the card number and the ISO/IEC 7812 prefixes.
     * This is an optional property that can be null if not provided.
     */
    private CreditCardBrandInterface|false|null $cardBrandFactory = null;

    /**
     * @var CreditCardInterface $instance
     * Cached Object
     */
    private static CreditCardInterface $instance;

    /**
     * Card constructor.
     *
     * @param string $number The credit card number.
     * which is typically a 3 or 4-digit number found on the back of the card.
     * This parameter is optional and can be null if not provided.
     * as it appears on the credit card. This parameter is optional and can be null if not provided.
     */
    public function __construct(
        public readonly string $number
    ) {
    }

    /**
     * Set the expiration date of the credit card.
     *
     * @param DateTimeInterface|string|null $expiry The expiration date of the credit card,
     * which can be provided as a DateTimeInterface object, a string in the format "MM/YY" or "MM/YYYY",
     * or null if the expiration date is not provided.
     *
     * @throws InvalidDataTypeException If the provided expiry format is invalid.
     */
    public function setExpiry(DateTimeInterface|string|null $expiry): void
    {
        if ($expiry === null) {
            $this->month = null;
            $this->year = null;
            return;
        }
        if ($expiry instanceof DateTimeInterface) {
            $this->month = $expiry->format('m');
            $this->year = $expiry->format('Y');
        } else {
            $parts = explode('/', $expiry);
            if (count($parts) !== 2) {
                throw new InvalidDataTypeException(
                    'MM/YY or MM/YYYY',
                    $expiry,
                    'Invalid expiry format. Expected MM/YY or MM/YYYY.'
                );
            }
            $month = trim($parts[0]);
            $year = trim($parts[1]);
            if (!ctype_digit($month) || (int)$month < 1 || (int)$month > 12) {
                throw new InvalidDataTypeException(
                    'numeric-string<01,12>',
                    $month,
                    'Invalid month format. Expected a numeric string between 01 and 12.'
                );
            }
            if (!ctype_digit($year) || (strlen($year) !== 2
                    && strlen($year) !== 4) || (
                    strlen($year) === 4
                    && ((int)$year < 1000 || (int)$year > 9999)
                )
            ) {
                throw new InvalidDataTypeException(
                    'numeric-string<1000,9999> or numeric-string<00,99>',
                    $year,
                    'Invalid year format. Expected a numeric string between 1000 and 9999.'
                );
            }
            if (strlen($year) === 2) {
                $currentYear = date('Y');
                $currentCentury = substr($currentYear, 0, 2);
                $year = $currentCentury . $year;
            }
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
            $this->month = $month;
            $this->year = $year;
        }
    }

    /**
     * Set the Card Verification Value (CVV) code for the credit card.
     *
     * @param string|null $cvv The CVV code, which is typically a 3 or 4-digit number found on the back of the card.
     * This parameter is optional and can be null if not provided.
     *
     * @throws InvalidDataTypeException If the provided CVV format is invalid.
     */
    public function setCVV(?string $cvv): void
    {
        if ($cvv === null) {
            $this->cvv = null;
            return;
        }
        if (!ctype_digit($cvv) || (strlen($cvv) !== 3 && strlen($cvv) !== 4)) {
            throw new InvalidDataTypeException(
                'numeric-string<100,9999>',
                $cvv,
                'Invalid CVV format. Expected a numeric string of 3 or 4 digits.'
            );
        }
        $this->cvv = $cvv;
    }

    /**
     * @return string|null
     */
    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    /**
     * Get the expiration date of the credit card in the format "MM/YYYY".
     *
     * @return string|null The expiration date as a string in the format "MM/YYYY",
     * or null if either the month or year is not set.
     */
    public function getExpiry(): ?string
    {
        if ($this->month === null || $this->year === null) {
            return null;
        }
        return $this->month . '/' . $this->year;
    }

    /**
     * Get the expiration month of the credit card.
     *
     * @return string|null The expiration month as a two-digit string (e.g., "01" for January), or null if not set.
     */
    public function getExpiryMonth(): ?string
    {
        return $this->month;
    }

    /**
     * Get the expiration year of the credit card.
     *
     * @return string|null The expiration year as a four-digit string (e.g., "2025"), or null if not set.
     */
    public function getExpiryYear(): ?string
    {
        return $this->year;
    }

    /**
     * Set the cardholder's name as it appears on the credit card.
     *
     * @param string|null $cardholderName The name of the cardholder, or null if not provided.
     */
    public function setCardholderName(?string $cardholderName): void
    {
        $this->cardholderName = $cardholderName;
    }

    /**
     * Get the cardholder's name.
     *
     * @return string|null The name of the cardholder, or null if not set.
     */
    public function getCardholderName(): ?string
    {
        return $this->cardholderName;
    }

    /**
     * Get the credit card number.
     *
     * @return string The credit card number as a string.
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Set the brand of the credit card.
     *
     * @param CreditCardBrandInterface $brand The brand of the credit card to set.
     *
     * @throws InvalidDataTypeException If the provided brand is not valid for the current card number.
     */
    public function setBrand(CreditCardBrandInterface $brand): void
    {
        if (!$brand->isValid($this->number)) {
            throw new InvalidDataTypeException(
                $brand->getName(),
                $this->getCardBrand()?->getName()??'N/A',
                'The provided brand is not valid for the current card number.'
            );
        }
        $this->cardBrandFactory = $brand;
    }

    /**
     * @inheritdoc
     */
    public function isValidCardNumber(): bool
    {
        if (isset($this->validCardNumber)) {
            return $this->validCardNumber;
        }
        $length = strlen($this->number);
        if ($length < CreditCardInterface::MIN_PAN_LENGTH
            || $length > CreditCardInterface::MAX_PAN_LENGTH
        ) {
            return $this->validCardNumber = false;
        }
        try {
            Luhn::assert($this->number);
        } catch (Throwable) {
            return $this->validCardNumber = false;
        }

        /**
         * @var non-empty-string $firstNumber
         * The first digit of the credit card number,
         * which is used to determine the card brand based on the ISO prefixes defined in the CreditCardInterface.
         * The first digit is expected to be a categorized industry.
         * @link  https://en.wikipedia.org/wiki/ISO/IEC_7812
         */
        $firstNumber = $this->number[0];
        /**
         * @var numeric-string $firstNumber
         */
        return $this->validCardNumber = isset(CreditCardInterface::IEC_7812_PREFIXES[$firstNumber]);
    }

    /**
     * Return a masked version of the credit card number when the object is treated as a string.
     * The masking typically replaces all but the last four digits of the card number with asterisks (*).
     *
     * @return string A masked version of the credit card number.
     */
    public function __toString(): string
    {
        return CreditCard::mask($this->number);
    }

    /**
     * @inheritdoc
     */
    public function getCardBrand(?CreditCardInterface $creditCard = null): ?CreditCardBrandInterface
    {
        if (!$creditCard) {
            if ($this->cardBrandFactory === null) {
                try {
                    $this->cardBrandFactory = (self::$instance ??= new CreditCard())->guess($this);
                } catch (Throwable) {
                    $this->cardBrandFactory = false;
                }
            }
            return $this->cardBrandFactory ?: null;
        }
        try {
            return $creditCard->guess($this);
        } catch (Throwable) {
            return null;
        }
    }
}
