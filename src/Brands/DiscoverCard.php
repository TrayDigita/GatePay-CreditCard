<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function strlen;
use function substr;

/**
 * Discover is a credit card brand issued primarily in the United States.
 * It was introduced by Sears in 1985 and is currently issued by Capital One
 *
 * @see https://en.wikipedia.org/wiki/Discover_Card
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class DiscoverCard extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Discover Card.
     * @var non-empty-lowercase-string
     */
    public const ID = 'discover';

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
    protected string $name = 'Discover Card';

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
        // 622126–622925 (China UnionPay co-branded)
        622126,
        622127,
        622128,
        622129,
        622130,
        622131,
        622132,
        622133,
        622134,
        622135,
        622136,
        622137,
        622138,
        622139,
        622140,
        622141,
        622142,
        622143,
        622144,
        622145,
        622146,
        622147,
        622148,
        622149,
        622150,
        622151,
        622152,
        622153,
        622154,
        622155,
        622156,
        622157,
        622158,
        622159,
        622160,
        622161,
        622162,
        622163,
        622164,
        622165,
        622166,
        622167,
        622168,
        622169,
        // default IIN ranges for Discover cards
        6011,
        644,
        645,
        646,
        647,
        648,
        649,
        65,
    ];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [16, 17, 18, 19];

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        // ranges 16-19 are valid for Discover cards,
        // but some sources suggest that 16 is the most common length
        if ($length > 19 || $length < 16) {
            return false;
        }
        if ($pan[0] !== '6') {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        // take 6 digits for IIN range 622126–622925, otherwise take 4 digits for other IIN ranges
        $digit = (int) substr($pan, 0, 6);
        if ($digit >= 622126 && $digit <= 622925) {
            return true;
        }
        // 6011, 644-649, 65 are valid IINs for Discover cards
        if (str_starts_with($pan, '6011')) {
            return true;
        }
        $digit = (int) substr($pan, 0, 3);
        if ($digit >= 644 && $digit <= 649) {
            return true;
        }
        return "$pan[0]$pan[1]" === '65';
    }
}
