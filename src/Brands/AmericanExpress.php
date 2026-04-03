<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function strlen;

/**
 * The American Express credit card brand, also known as Amex, is a widely recognized and accepted payment card brand.
 * American Express cards are known for their distinctive design and are often associated
 *      with premium services and rewards programs.
 *
 * @see https://en.wikipedia.org/wiki/American_Express
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class AmericanExpress extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the American Express credit card brand.
     * @var non-empty-lowercase-string
     */
    public const ID = 'amex';

    /**
     * The unique identifier for the credit card brand.
     * This is typically a lowercase string that represents the brand (e.g., "visa", "mastercard").
     *
     * @var non-empty-lowercase-string $id
     */
    protected string $id = self::ID;

    /**
     * The human-readable name of the credit card brand.
     * This is typically a capitalized string that represents the brand (e.g., "Visa", "MasterCard").
     *
     * @var non-empty-string $name
     */
    protected string $name = 'American Express';

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList = [34, 37];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [15];

    /**
     * @var non-empty-list<int<3,4>> $cvvLength
     * The length of the Card Verification Value (CVV) for this credit card brand.
     * By default, it is set to 3, which is common for most credit card brands (e.g., Visa, MasterCard).
     */
    protected array $cvvLength = [4];

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        if (strlen($pan) !== 15) {
            return false;
        }
        if (((int)$pan[0]) !== 3) {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        $prefix = "$pan[0]$pan[1]";
        return $prefix === '34' || $prefix === '37';
    }
}
