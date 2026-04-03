<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function range;
use function strlen;
use function substr;

/**
 * JCB (Japan Credit Bureau) is a credit card company based in Japan that offer
 * s a range of credit card products and services.
 * JCB cards are widely accepted in Japan and are also accepted internationally, particularly in Asia.
 *
 * @see https://en.wikipedia.org/wiki/Japan_Credit_Bureau
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class JCB extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the JCB.
     * @var non-empty-lowercase-string
     */
    public const ID = 'jcb';

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
    protected string $name = 'JCB';

    /**
     * An array of valid Issuer Identification Number (IIN/BIN) ranges for this credit card brand.
     * Each range can be represented as a string of digits (e.g., "4" for Visa, "51-55" for MasterCard)
     * or as a positive integer (e.g., 4 for Visa, 51 for MasterCard).
     *
     * @var non-empty-list<positive-int> $iinList
     * @see https://www.iso.org/standard/70484.html
     * @see @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
     */
    protected array $iinList;

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
     * Constructor to initialize the IIN list for JCB cards.
     * JCB cards typically have IINs in the range of 3528 to 3589, inclusive.
     */
    public function __construct()
    {
        // 3528–3589
        $this->iinList = range(3528, 3589);
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length > 19 || $length < 16) {
            return false;
        }
        if ("$pan[0]$pan[1]" !== '35') {
            return false;
        }
        try {
            // assert that the PAN is valid according to the Luhn algorithm,
            // which is commonly used for validating credit card numbers
            Luhn::assert($pan);
        } catch (Throwable) {
            return false;
        }
        $prefix = (int)substr($pan, 0, 4);
        return $prefix >= 3528 && $prefix <= 3589;
    }
}
