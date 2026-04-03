<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use GatePay\CreditCard\CardType;
use Throwable;
use function strlen;

/**
 * Solo was a debit card in the United Kingdom introduced as a sister to the then existing Switch.
 * (Later merged with the Maestro debit card brand of the Mastercard corporation) Launched on 1 July 1997,
 * by the Switch Card Scheme,[1] it was designed for use on deposit accounts,
 * as well as by customers who did not qualify for a Switch card (or, later, Maestro card) on current accounts,
 * such as teenagers. The Solo card scheme was decommissioned permanently on 31 March 2011.
 *
 * @see https://en.wikipedia.org/wiki/Solo_(debit_card)
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class Solo extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Solo Card.
     * @var non-empty-lowercase-string
     */
    public const ID = 'solo';

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
    protected string $name = 'Solo';

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
        6334,
        6767
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
        if (!str_starts_with($pan, '6334') && !str_starts_with($pan, '6767')) {
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
