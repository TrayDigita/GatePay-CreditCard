<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function str_starts_with;
use function strlen;

/**
 * Elo is a Brazilian credit card brand that was created in 2011 as a joint venture between three major
 * Brazilian banks: Banco do Brasil, Bradesco, and Caixa Econômica Federal.
 * Elo cards are widely accepted in Brazil and are also accepted internationally in some locations.
 * The IIN/BIN ranges for Elo cards include various six-digit prefixes, and the PAN length is typically 16 digits.
 *
 * @see https://en.wikipedia.org/wiki/Elo_(card_association)
 * @see https://binlist.io/scheme/elo/
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class Elo extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the BankCard credit card brand.
     * @var non-empty-lowercase-string
     */
    public const ID = 'elo';

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
    protected string $name = 'Elo';

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
        504175,
        506699,
        506700,
        506704,
        506714,
        506716,
        506718,
        506721,
        506722,
        506724,
        506725,
        506726,
        506727,
        506728,
        506729,
        506730,
        506731,
        506732,
        506733,
        506735,
        506741,
        506742,
        506744,
        506748,
        506753,
        506754,
        506755,
        506758,
        506760,
        506761,
        506762,
        506764,
        506766,
        506767,
        506770,
        506771,
        506774,
        506775,
        506776,
        506778,
        509000,
        509002,
        509003,
        509008,
        509014,
        509015,
        509017,
        509018,
        509032,
        509034,
        509041,
        509042,
        509045,
        509048,
        509051,
        509055,
        509059,
        509061,
        509063,
        509065,
        509067,
        509068,
        509069,
        509073,
        509091,
        509093,
        509291,
        509728,
        509882,
        636297,
        636368
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
        if ($pan[0] === '5') {
            if ($pan[1] !== '0') {
                return false;
            }
            if ($pan[2] === '4') {
                if ($pan[3] !== '1' || $pan[4] !== '7' || $pan[5] !== '5') {
                    return false;
                }
            } elseif ($pan[2] === '0') {
                if ($pan[3] !== '6' || $pan[4] !== '9' || $pan[5] < '9' || $pan[5] > '8') {
                    return false;
                }
            } elseif ($pan[2] === '9') {
                if ($pan[3] !== '0' || $pan[4] !== '0' || $pan[5] < '0' || $pan[5] > '9') {
                    return false;
                }
            } else {
                return false;
            }
        } elseif ($pan[0] === '6') {
            if (!str_starts_with($pan, '636297') && !str_starts_with($pan, '636368')) {
                return false;
            }
        } else {
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
