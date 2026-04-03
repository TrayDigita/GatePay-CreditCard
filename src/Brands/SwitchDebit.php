<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\CardType;
use Throwable;
use function strlen;

/**
 * Switch is debit card from United Kingdom
 *
 * @see https://en.wikipedia.org/wiki/Switch_(debit_card)
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class SwitchDebit extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Switch.
     * @var non-empty-lowercase-string
     */
    public const ID = 'switch';

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
    protected string $name = 'Switch';

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
        564182,
        633110,
        4903,
        4905,
        4911,
        4936,
        6333,
        6759
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
     * Solo cards are debit cards, which means they are linked to a bank account
     * and can only be used to spend money that is already in the account.
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
        if ($length !== 16 && $length !== 18 && $length !== 19) {
            return false;
        }
        if ($pan[0] ==='4' && $pan[1] !== '9') {
            return false;
        }
        if ($pan[0] === '6' && ($pan[1] !== '3' && $pan[1] !== '7')) {
            return false;
        }
        if ($pan[0] === '5' && !str_starts_with($pan, '564182')) {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        foreach ($this->iinList as $iin) {
            if (str_starts_with($pan, (string)$iin)) {
                return true;
            }
        }
        return false;
    }
}
