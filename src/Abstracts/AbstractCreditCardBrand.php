<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Abstracts;

use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use GatePay\CreditCard\Interfaces\CreditCardInterface;
use Throwable;
use function array_rand;
use function in_array;
use function mt_rand;
use function rsort;
use function str_starts_with;
use function strlen;
use const SORT_NUMERIC;

/**
 * Abstract class representing a credit card brand,
 * providing common properties and structure for specific credit card brand implementations.
 * This class is intended to be extended by concrete classes that represent specific credit card brands
 * (e.g., Visa, MasterCard, American Express).
 *
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
abstract class AbstractCreditCardBrand implements CreditCardBrandInterface
{
    /**
     * The unique identifier for the credit card brand.
     * This is typically a lowercase string that represents the brand (e.g., "visa", "mastercard").
     *
     * @var non-empty-lowercase-string $id
     */
    protected string $id;

    /**
     * The human-readable name of the credit card brand.
     * This is typically a capitalized string that represents the brand (e.g., "Visa", "MasterCard").
     *
     * @var non-empty-string $name
     */
    protected string $name;

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList;

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths;

    /**
     * @var non-empty-list<int<3,4>> $cvvLength
     * The length of the Card Verification Value (CVV) for this credit card brand.
     * By default, it is set to 3, which is common for most credit card brands (e.g., Visa, MasterCard).
     */
    protected array $cvvLength = [3];

    /**
     * @var CardType $cardType
     * The type of card, which is set to CardType::CREDIT by default.
     * This indicates that the card is a credit card,
     * as opposed to a debit card or other types of payment cards.
     * By default, this property is set to CardType::CREDIT, but it can be overridden in subclasses if needed
     *
     * @note This only reference, maybe getting false positives,
     * as some brands can be both credit and debit cards, but for the sake of simplicity,
     * we will assume that all brands are credit cards unless specified otherwise in the subclass.
     */
    protected CardType $cardType = CardType::CREDIT;

    /**
     * @inheritdoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getIINList(): array
    {
        return $this->iinList;
    }

    /**
     * @inheritdoc
     */
    public function getPANLengths(): array
    {
        return $this->panLengths;
    }

    /**
     * @inheritdoc
     */
    public function getCVVLengths(): array
    {
        return $this->cvvLength;
    }

    /**
     * @inheritdoc
     */
    public function getType(): CardType
    {
        return $this->cardType;
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length > CreditCardInterface::MAX_PAN_LENGTH
            || $length < CreditCardInterface::MIN_PAN_LENGTH
        ) {
            return false;
        }
        try {
            // assert that the PAN length is valid for this credit card brand
            if (!in_array($length, $this->getPANLengths(), true)) {
                return false;
            }
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
            // Safe access just make sure the IIN list is valid
            // and can be retrieved without throwing an exception
            $this->iinList = $this->getIINList();
        } catch (Throwable) {
            return false;
        }
        // sort array by biggest to smallest, to ensure the longest IIN is checked first
        rsort($this->iinList, SORT_NUMERIC);
        foreach ($this->iinList as $iin) {
            if (str_starts_with($pan, (string)$iin)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function generateCVV(): string
    {
        $lengths = $this->getCVVLengths();
        $length = $lengths[array_rand($lengths)];
        $cvv = '';
        for ($i = 0; $i < $length; $i++) {
            $cvv .= mt_rand(0, 9);
        }
        return $cvv;
    }

    /**
     * Get the nearest valid PAN length for this credit card brand based on the provided possible nearest length.
     *
     * If a possible nearest length is provided, this method will
     * return the nearest valid PAN length that is greater than or equal to it.
     * If no possible nearest length is provided, this method will
     * randomly select a valid PAN length from the available lengths for this credit card brand.
     *
     * @param int|null $possibleNearestLength optional parameter that suggests a preferred length for the generated PAN.
     * @return positive-int The nearest valid PAN length for this credit card brand based provided possible nearest.
     * or a randomly selected valid PAN length if no possible nearest length is provided.
     */
    protected function getNearestRandomLength(?int $possibleNearestLength): int
    {
        $lengths = $this->getPANLengths();
        // getting the nearest length to the possible nearest length if provided,
        // otherwise randomly select a length from the available PAN lengths
        if ($possibleNearestLength !== null) {
            $nearestLength = null;
            foreach ($lengths as $len) {
                if ($len >= $possibleNearestLength) {
                    $nearestLength = $len;
                    break;
                }
            }
            if ($nearestLength !== null) {
                $possibleNearestLength = $nearestLength;
            } else {
                // If no valid length is found that is greater than or equal
                // to the possible nearest length,
                // randomly select a length from the available PAN lengths
                $possibleNearestLength = $lengths[array_rand($lengths)];
            }
        } else {
            // Randomly select a length from the available PAN lengths
            $possibleNearestLength = $lengths[array_rand($lengths)];
        }
        return $possibleNearestLength;
    }

    /**
     * Generate a random IIN from the available IIN list for this credit card brand.
     *
     * @param positive-int $length The length of the card number to be generated,
     *      which can be used to filter the IIN list
     *      if needed (e.g., some IINs may only be valid for certain card number lengths).
     * @return string A randomly selected IIN from the available IIN list for this credit card brand.
     */
    protected function generateRandomIIN(int $length): string
    {
        $iinList = $this->getIINList();
        // Randomly select an IIN
        return (string)$iinList[array_rand($iinList)];
    }

    /**
     * @inheritdoc
     */
    public function generate(?int $possibleNearestLength = null): string
    {
        $possibleNearestLength = $this->getNearestRandomLength($possibleNearestLength);
        $iin = $this->generateRandomIIN($possibleNearestLength);

        // calculate how many random digits we need to generate in the middle of the card number
        // The total length of the card number should be equal to the possible nearest length,
        // reduced by the length of the IIN and reduced by 1 (for the check digit at the end)
        $numDigitsToGenerate = $possibleNearestLength - strlen($iin) - 1;

        /**
         * @var numeric-string $iin
         */
        // Generate random digits
        $randomDigits = '';
        for ($i = 0; $i < $numDigitsToGenerate; $i++) {
            $randomDigits .= mt_rand(0, 9);
        }

        // concat the IIN and the random digits to form the partial card number before calculating the check digit
        /**
         * @var numeric-string $partialNumber
         */
        $partialNumber = $iin . $randomDigits;

        // calculate Luhn check digit for the partial card number (which is the IIN + random digits)
        $sum = 0;
        $length = strlen($partialNumber);
        // calculate weights for the digits in the partial number according to the Luhn algorithm,
        // which requires doubling every second digit from the right
        // (starting with the rightmost digit, which is the check digit position).
        for ($i = 0; $i < $length; $i++) {
            $digit = (int)$partialNumber[$length - 1 - $i];

            // multiply every second digit by 2, starting from the rightmost digit (which is the check digit position)
            if ($i % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        // Checking digit is the number that needs to be added to the sum of the digits
        // (after applying the Luhn algorithm) to make it a multiple of 10.
        $checkDigit = (10 - ($sum % 10)) % 10;

        // concat the partial number with the check digit to form the full card number
        return $partialNumber . $checkDigit;
    }

    /**
     * Return the name of the credit card brand when the object is treated as a string.
     *
     * @return string The name of the credit card brand.
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
