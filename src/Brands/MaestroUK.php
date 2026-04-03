<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\CardType;
use Throwable;
use function str_starts_with;
use function strlen;

/**
 * Maestro UK is a debit card brand that was issued in the United Kingdom and is a subsidiary of Mastercard.
 *
 * @see https://en.wikipedia.org/wiki/Maestro_(debit_card)
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class MaestroUK extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Maestro UK card brand.
     * @var non-empty-lowercase-string
     */
    public const ID = 'maestro-uk';

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
    protected string $name = 'Maestro UK';

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
        676770,
        676774,
        6759,
    ];

    /**
     * An array of valid Primary Account Number (PAN) lengths for this credit card brand.
     * The PAN is the full card number, which includes the IIN/BIN and the individual account identifier.
     * Valid PAN lengths should be between 8 and 19 digits, inclusive.
     *
     * @var non-empty-list<int<8,19>> $panLengths
     * @see https://www.iso.org/standard/70484.html
     */
    protected array $panLengths = [
        // 12–19
        12, 13, 14, 15, 16, 17, 18, 19
    ];

    /**
     * The type of card, which can be either, the Maestro UK card brand is a debit card brand.
     *
     * @var CardType $cardType
     */
    protected CardType $cardType = CardType::DEBIT;

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length < 12 || $length > 19) {
            return false;
        }
        if ("$pan[0]$pan[1]" !== '67') {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        return str_starts_with($pan, '676770') ||
            str_starts_with($pan, '676774') ||
            str_starts_with($pan, '6759');
    }
}
