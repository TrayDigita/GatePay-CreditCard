<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * GPN (Gerbang Pembayaran Nasional) is Indonesia's national payment gateway,
 * The GPN payment system is designed to facilitate secure and efficient electronic transactions within Indonesia,
 *
 * @see https://en.wikipedia.org/wiki/Gerbang_Pembayaran_Nasional
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class GPN extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the GPN.
     * @var non-empty-lowercase-string
     */
    public const ID = 'gpn';

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
    protected string $name = 'GPN (Gerbang Pembayaran Nasional)';

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
        // 1946 (BNI cards)
        1946, // is not RFC 1946, but is a valid IIN for GPN cards issued by BNI (Bank Negara Indonesia)
        //50, 56, 58, 60–63
        50,
        56,
        58,
        60,
        61,
        62,
        63
    ];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [16, 18, 19];

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length !== 16 && $length !== 18 && $length !== 19) {
            return false;
        }
        if ($pan[0] === '1' && !str_starts_with($pan, '1946')) {
            return false;
        }
        if ($pan[0] === '5') {
            if (!str_starts_with($pan, '50') && !str_starts_with($pan, '56') && !str_starts_with($pan, '58')) {
                return false;
            }
        }
        if ($pan[0] === '6') {
            $prefixTwo = (int)substr($pan, 0, 2);
            if ($prefixTwo < 60 || $prefixTwo > 63) {
                return false;
            }
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
