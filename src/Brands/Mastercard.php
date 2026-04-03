<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function array_merge;
use function array_rand;
use function range;
use function strlen;

/**
 * MasterCard is a global payment technology company that provides a range of financial services,
 * including credit card processing and payment solutions.
 * MasterCard offers various credit card products and services to consumers and businesses worldwide.
 *
 * @see https://en.wikipedia.org/wiki/Visa_Inc
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class Mastercard extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the MasterCard card brand.
     * @var non-empty-lowercase-string
     */
    public const ID = 'mastercard';

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
    protected string $name = 'Mastercard';

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
     * @note 51-55 have 16 digits, 2221–2720 have 16, 18, or 19 digits
     */
    protected array $panLengths = [16];

    /**
     * @inheritdoc
     */
    public function getIINList(): array
    {
        // 2221–2720 & 51–55
        return $this->iinList ??= array_merge(range(2221, 2720), range(51, 55));
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length !== 16) {
            return false;
        }
        if ($pan[0] === '2') {
            $prefix = (int)substr($pan, 0, 4);
            if ($prefix < 2221 || $prefix > 2720) {
                return false;
            }
        } else {
            $prefix = (int)substr($pan, 0, 2);
            if ($prefix < 51 || $prefix > 55) {
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

    /**
     * Generate a random IIN from the available IIN list for this credit card brand.
     *
     * @param positive-int $length The length of the card number to be generated,
     *      which can be used to filter the IIN list
     *      if needed (e.g., some IINs may only be valid for certain card number lengths).
     * @return string A randomly selected IIN from the available IIN list for this credit card brand.
     */
    protected function generateRandomIIN(int $length): string
    {
        $iinList  = $this->getIINList();
        // Randomly select an IIN
        return (string)$iinList[array_rand($iinList)];
    }
}
