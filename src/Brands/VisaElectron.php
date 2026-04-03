<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\CardType;
use Throwable;
use function strlen;

/**
 * Visa Electron is a debit card brand that is issued by Visa and is designed for use in electronic transactions,
 * such as online purchases and point-of-sale transactions.
 * It is a variant of the Visa card and is typically linked to a bank account,
 * allowing cardholders to access their funds directly for transactions.
 *
 * @see https://en.wikipedia.org/wiki/Visa_Electron
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class VisaElectron extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the VISA Electron.
     * @var non-empty-lowercase-string
     */
    public const ID = 'visa-electron';

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
    protected string $name = 'Visa Electron';

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
        417500,
        4026,
        4844,
        4913,
        4917
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
     * Visa Electron cards are typically debit cards, which means they are linked directly to a bank account.
     *
     * @var CardType $cardType
     */
    protected CardType $cardType = CardType::DEBIT;

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        if (strlen($pan) !== 16) {
            return false;
        }
        if ($pan[0] !== '4') {
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
