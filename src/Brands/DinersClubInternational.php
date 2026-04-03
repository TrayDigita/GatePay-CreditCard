<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function in_array;
use function strlen;

/**
 * Diners Club International Ltd. (DCI), founded as Diners Club, is a charge card company owned by Capital One.
 * Formed in 1950 by Frank X. McNamara, Ralph Schneider (1909–1964),[3] Matty Simmons, and Alfred S. Bloomingdale,
 * it was the first independent payment card company in the world, successfully establishing the financial card
 * service of issuing travel and entertainment (T&E) credit cards as a viable business.
 *
 * @see https://en.wikipedia.org/wiki/Diners_Club_International
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class DinersClubInternational extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Diners Club International.
     * @var non-empty-lowercase-string
     */
    public const ID = 'dci';

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
    protected string $name = 'Diners Club International';

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList = [30, 36, 38, 39];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [14, 15, 16, 17, 18, 19];

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length < 14 || $length > 19) {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        $prefix = "$pan[0]$pan[1]"; // only 2 digits are needed for DCI IINs
        return in_array((int)$prefix, $this->iinList, true);
    }
}
