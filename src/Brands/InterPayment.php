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
 * Inter Payment (known as Decoupled debit card) a debit card in the US
 * that is not issued by and not tied to any particular retail financial institution,
 * such as a bank or credit union. This is based on the ability in the US ACH Network payment system
 * to make an electronic payment from any bank or credit union without
 * needing to use a card issued by the bank or credit union.
 *
 * @see https://en.wikipedia.org/wiki/Decoupled_debit_card
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class InterPayment extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Inter Payment.
     * @var non-empty-lowercase-string
     */
    public const ID = 'inter-payment';

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
    protected string $name = 'Interchange Payment (Debit Card)';

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList = [636];

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
     * @var CardType $cardType The type of card, which can be either credit or debit.
     * In this case, it is set to debit.
     */
    protected CardType $cardType = CardType::DEBIT;

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length < 16 || $length > 19) {
            return false;
        }
        if (!str_starts_with($pan, '636')) {
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
