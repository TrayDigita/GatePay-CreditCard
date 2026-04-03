<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function strlen;
use function substr;

/**
 * The HiperCard credit card brand is a popular payment card in Brazil, issued by the Brazilian company Hipercard.
 * HiperCard cards are widely accepted in Brazil and are also accepted internationally in some locations.
 *
 * @see https://www.hipercard.com.br/
 * @see https://bintable.com/scheme/HIPERCARD
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class HiperCard extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the BankCard credit card brand.
     * @var non-empty-lowercase-string
     */
    public const ID = 'hipercard';

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
    protected string $name = 'HiperCard';

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList = [
        384100,
        384140,
        384160,
        606282,
        637095,
        637568,
        637599,
        637609,
        637612,
    ];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [16];

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        if (strlen($pan) !== 16) {
            return false;
        }
        $sixDigitPrefix = substr($pan, 0, 6);
        if ($sixDigitPrefix !== '384100' &&
            $sixDigitPrefix !== '384140' &&
            $sixDigitPrefix !== '384160' &&
            $sixDigitPrefix !== '606282' &&
            $sixDigitPrefix !== '637095' &&
            $sixDigitPrefix !== '637568' &&
            $sixDigitPrefix !== '637599' &&
            $sixDigitPrefix !== '637609' &&
            $sixDigitPrefix !== '637612'
        ) {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        return true;
    }
}
