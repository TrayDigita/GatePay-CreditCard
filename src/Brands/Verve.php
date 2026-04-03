<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\Algorithms\Luhn;
use Throwable;
use function array_merge;
use function range;
use function strlen;
use function substr;

/**
 * Verve International is a Nigerian Pan-African and multinational financial technology and
 * payment card brand owned by Interswitch Group.
 *
 * @see https://en.wikipedia.org/wiki/Verve_International
 * @see https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)
 */
final class Verve extends AbstractCreditCardBrand
{
    /**
     * The unique identifier for the Verve.
     * @var non-empty-lowercase-string
     */
    public const ID = 'verve';

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
    protected string $name = 'Verve';

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
     * @note Lazy loading the IIN list to avoid unnecessary memory usage if the list is not needed.
     */
    public function getIINList(): array
    {
        return $this->iinList ??= array_merge(
            range(507865, 507964),
            range(506099, 506198),
            range(650002, 650027),
        );
    }

    /**
     * @inheritdoc
     */
    public function isValid(string $pan): bool
    {
        $length = strlen($pan);
        if ($length !== 16 && $length !== 18 && $length !== 19) {
            return false;
        }
        // 506099–506198, 507865–507964, 650002–650027,
        $sixDigits = (int) substr($pan, 0, 6);
        if ($sixDigits >= 650002) {
            if ($sixDigits > 650027) {
                return false;
            }
        } elseif ($sixDigits >= 507865) {
            if ($sixDigits > 507964) {
                return false;
            }
        } elseif ($sixDigits >= 506099) {
            if ($sixDigits > 506198) {
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
