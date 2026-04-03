<?php
declare(strict_types=1);

namespace GatePay\CreditCard;

use GatePay\CreditCard\Brands\AmericanExpress;
use GatePay\CreditCard\Brands\BankCard;
use GatePay\CreditCard\Brands\Borica;
use GatePay\CreditCard\Brands\ChinaTUnion;
use GatePay\CreditCard\Brands\Dankort;
use GatePay\CreditCard\Brands\DinersClub;
use GatePay\CreditCard\Brands\DinersClubInternational;
use GatePay\CreditCard\Brands\DiscoverCard;
use GatePay\CreditCard\Brands\Elo;
use GatePay\CreditCard\Brands\HiperCard;
use GatePay\CreditCard\Brands\Humo;
use GatePay\CreditCard\Brands\InterPayment;
use GatePay\CreditCard\Brands\JCB;
use GatePay\CreditCard\Brands\LankaPay;
use GatePay\CreditCard\Brands\Laser;
use GatePay\CreditCard\Brands\Maestro;
use GatePay\CreditCard\Brands\MaestroUK;
use GatePay\CreditCard\Brands\Mastercard;
use GatePay\CreditCard\Brands\Mir;
use GatePay\CreditCard\Brands\RuPay;
use GatePay\CreditCard\Brands\Solo;
use GatePay\CreditCard\Brands\SwitchDebit;
use GatePay\CreditCard\Brands\Troy;
use GatePay\CreditCard\Brands\UnionPay;
use GatePay\CreditCard\Brands\UzCard;
use GatePay\CreditCard\Brands\Verve;
use GatePay\CreditCard\Brands\Visa;
use GatePay\CreditCard\Brands\VisaElectron;
use GatePay\CreditCard\Interfaces\CardInterface;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use function count;
use function get_class;
use function is_object;
use function is_string;
use function str_repeat;
use function strlen;
use function strtolower;
use function substr;
use function trim;

/**
 * CreditCard class serves as a collection and manager for various credit card brands.
 * It provides functionality to add, remove, and retrieve credit card brands,
 * as well as to guess the brand of a credit card based on its Primary Account Number (PAN).
 * The class is designed to be flexible and extensible,
 * allowing for the inclusion of a wide range of credit card brands,
 *
 * @template TKey of non-empty-lowercase-string
 */
final class CreditCard implements Interfaces\CreditCardInterface
{
    /**
     * An array of core credit card brand classes that are included by default in the collection.
     * These classes represent the fundamental credit card brands that are supported by the system.
     * The order of the classes in this array may be significant, as it can affect the way brands are matched
     * when determining the brand of a given credit card number. For example, more specific brands should be listed
     * before more general ones to ensure accurate brand identification.
     *
     * @var array<non-empty-lowercase-string, class-string<CreditCardBrandInterface>>
     */
    public const FACTORY_CARDS = [
        // -----------------------------------------------------------------
        // Tier 1 — 6-digit IIN prefix (most specific, checked first)
        // -----------------------------------------------------------------

        // 357111 — JCB co-branded; must precede JCB (3571 ∈ [3528–3589])
        LankaPay::ID => LankaPay::class,

        // 560221–560225 (6-digit) + 5610 (4-digit) — must precede GPN (56)
        BankCard::ID => BankCard::class,

        // 504175, 506699, 506700, 506704, 506714, 509000–509999, 650031–650033 (6-digit) + 651652–651679 (4-digit)
        // must precede GPN (50/65) and DiscoverCard/Troy/RuPay (65)
        Elo::ID => Elo::class,

        // 384100, 384140, 384160, 606282, 637095, 637568, 637599 (6-digit) — must precede GPN (38/60/62/63)
        HiperCard::ID => HiperCard::class,

        // 564182, 633110 (6-digit) + 4903/4905/4911/4936/6333/6759 (4-digit)
        // must precede MaestroUK, Maestro and Visa (4xxx)
        SwitchDebit::ID => SwitchDebit::class,

        // 676770, 676774 (6-digit) + 6759 (4-digit)
        // must precede Maestro (6759) and Solo (6767)
        MaestroUK::ID => MaestroUK::class,

        // 417500 (6-digit) + 4026/4844/4913/4917 (4-digit)
        // must precede Visa (4)
        VisaElectron::ID => VisaElectron::class,

        // 507865–507964, 506099–506198, 650002–650027 (all 6-digit)
        // must precede DiscoverCard/Troy/RuPay (65x) and GPN (50x)
        Verve::ID => Verve::class,

        // 622126–622169 (6-digit, UnionPay co-branded) + 6011/644-649/65 (shorter)
        // must precede UnionPay (62) and GPN (62)
        DiscoverCard::ID => DiscoverCard::class,

        // -----------------------------------------------------------------
        // Tier 2 — 4-digit IIN prefix
        // -----------------------------------------------------------------

        // 5019, 4571 — must precede Visa (4) and GPN (50)
        Dankort::ID => Dankort::class,

        // 2205 — 22xx area; must precede Mastercard (range starts at 2221)
        Borica::ID => Borica::class,

        // 2200–2204 — 22xx area; must precede Mastercard (range starts at 2221)
        Mir::ID => Mir::class,

        // 9860 — unique IIN, no major overlap
        Humo::ID => Humo::class,

        // 8600, 5614 — 5614 must precede GPN (56)
        UzCard::ID => UzCard::class,

        // 9792 (unique) + 65 (2-digit, Discover co-branded in Turkey)
        // must precede DiscoverCard and RuPay for the '65' prefix
        Troy::ID => Troy::class,

        // 6334, 6767 — MaestroUK (676770) already handles 6767x more specifically
        Solo::ID => Solo::class,

        // 6304, 6706, 6771, 6709 — 6304 overlaps with Maestro; must precede Maestro
        Laser::ID => Laser::class,

        // -----------------------------------------------------------------
        // Tier 3 — 3-digit IIN prefix
        // -----------------------------------------------------------------

        // 353, 356 (JCB co-branded) must precede JCB (353x/356x ∈ [3528–3589])
        // 508 must precede GPN (50); 60/65/81/82 must precede GPN
        RuPay::ID => RuPay::class,

        // 3528–3589 (4-digit range) — after LankaPay and RuPay co-branded ranges
        JCB::ID => JCB::class,

        // 5018/5020/5038/5893/6304/6759/6761/6762/6763
        // Mastercard subsidiary — must precede Mastercard and GPN
        Maestro::ID => Maestro::class,

        // 636 — must precede GPN (63)
        InterPayment::ID => InterPayment::class,

        // -----------------------------------------------------------------
        // Tier 4 — 2-digit IIN prefix
        // -----------------------------------------------------------------

        // 34, 37 — unique to AmericanExpress
        AmericanExpress::ID => AmericanExpress::class,

        // 55 — must precede Mastercard (range 51–55 includes 55)
        DinersClub::ID => DinersClub::class,

        // 30, 36, 38, 39 — unique to Diners Club International
        DinersClubInternational::ID => DinersClubInternational::class,

        // 31 — 19-digit PAN only; unique
        ChinaTUnion::ID => ChinaTUnion::class,

        // 62 — must come after DiscoverCard (622xxx), before GPN (62)
        UnionPay::ID => UnionPay::class,

        // 51–55, 2221–2720 — after DinersClub (55) and all Mastercard subsidiaries
        Mastercard::ID => Mastercard::class,

        // -----------------------------------------------------------------
        // Tier 5 — 1-digit IIN prefix
        // -----------------------------------------------------------------

        // 4 — must come after all Visa-family brands (VisaElectron, Dankort, SwitchDebit)
        Visa::ID => Visa::class,

        // -----------------------------------------------------------------
        // Tier 6 — Widest IIN coverage (catch-all, always last)
        // -----------------------------------------------------------------

        // 1946 (4-digit) + 50/56/58/60/61/62/63 (2-digit) — broadest overlap with many brands
        // GPN::ID => GPN::class,
    ];

    /**
     * A static cache to store instantiated credit card brand objects.
     * This cache is used to avoid redundant instantiation of brand classes,
     * improving performance when the same brands are accessed multiple times.
     * The keys are the normalized brand identifiers (lowercase strings),
     * and the values are the corresponding instantiated brand objects.
     * This allows for quick retrieval of brand instances without needing to re-instantiate
     *
     * @var array<class-string<CreditCardBrandInterface>, CreditCardBrandInterface|string> $cachedFactoryCards
     */
    private static array $cachedFactoryCards;

    /**
     * @var array<TKey, CreditCardBrandInterface|class-string<CreditCardBrandInterface>> $brands
     * An associative array where the keys are lowercase strings representing the credit card brand identifiers
     * (e.g., "visa", "mastercard") and the values are either instances.
     * Late initialization is used to allow for lazy instantiation of credit card brand classes,
     * which can improve performance by only creating instances when they are actually needed. Initially,
     * the array contains class names as strings,
     */
    private array $brands = self::FACTORY_CARDS;

    /**
     * @var bool $initialized
     * Defaults to false, indicating that the credit card brand collection has not been initialized.
     */
    private bool $initialized = false;

    /**
     * CreditCard constructor.
     * Accepts a variable number of brand identifiers (e.g., "visa", "mastercard") to exclude from the collection.
     * The constructor normalizes each provided brand identifier and removes it from the collection if it exists.
     *
     * @param string|TKey ...$brandIds A variable number of brand identifiers to exclude from the collection.
     */
    public function __construct(string ...$brandIds)
    {
        foreach ($brandIds as $brandId) {
            $normalizedBrandId = $this->normalizeBrandId($brandId);
            if (isset($this->brands[$normalizedBrandId])) {
                unset($this->brands[$normalizedBrandId]); // Remove the brand from the collection
            }
        }
    }

    /**
     * Creates a new instance of the specified credit card brand class or retrieve
     * it from the cache if it has already been instantiated.
     * @template T of CreditCardBrandInterface
     * @param class-string<T> $className
     * @return T
     */
    private function createNewOrUse(string $className): CreditCardBrandInterface
    {
        if (empty(self::$cachedFactoryCards)) {
            self::$cachedFactoryCards = [];
            foreach (self::FACTORY_CARDS as $id => $className) {
                self::$cachedFactoryCards[$className] = $id;
            }
        }
        if (is_object(self::$cachedFactoryCards[$className] ?? null)) {
            /**
             * @var T $brand
             */
            $brand = self::$cachedFactoryCards[$className];
            return $brand;
        }
        $instance = new $className();
        $className = get_class($instance);
        if (is_string(self::$cachedFactoryCards[$className] ?? null)) {
            self::$cachedFactoryCards[$className] = $instance;
        }
        return $instance;
    }

    /**
     * Normalizes the credit card brand identifier by trimming whitespace and converting it to lowercase.
     * This ensures that brand identifiers are stored and accessed in a consistent format,
     * regardless of how they are input.
     *
     * @template T of CreditCardBrandInterface
     * @param string|T $id The credit card brand identifier (e.g., "visa", "mastercard").
     * @return TKey
     */
    public function normalizeBrandId(string|CreditCardBrandInterface $id): string
    {
        if (is_string($id) && isset(self::$cachedFactoryCards[$id])) {
            $instance = self::$cachedFactoryCards[$id];
            if (is_object($instance)) {
                $instance = $instance->getId();
            }
            /**
             * @var TKey $instance
             */
            return $instance;
        }
        $id = is_object($id) ? $id->getId() : $id;
        /**
         * @var TKey $id
         */
        $id = strtolower(trim($id));
        return $id;
    }

    /**
     * @inheritdoc
     */
    public function has(string $brandId): bool
    {
        return isset($this->brands[$this->normalizeBrandId($brandId)]);
    }

    /**
     * @inheritdoc
     */
    public function getBrands(): array
    {
        $brands = $this->brands;
        if (!$this->initialized) {
            $this->initialized = true;
            foreach ($this->brands as $brandId => $brand) {
                if (is_string($brand)) {
                    $this->brands[$brandId] = self::createNewOrUse($brand);
                }
            }
            $brands = $this->brands;
        }

        /**
         * @var array<TKey, CreditCardBrandInterface> $brands
         */
        return $brands;
    }

    /**
     * @inheritdoc
     */
    public function get(string $brandId): CreditCardBrandInterface
    {
        $originalBrandId = $brandId;
        $brandId = $this->normalizeBrandId($brandId);
        $brand = $this->brands[$brandId] ?? null;
        if (is_string($brand)) {
            $brand = $this->brands[$brandId] = self::createNewOrUse($brand);
        }
        if (is_object($brand)) {
            return $brand;
        }
        throw new Exceptions\NotFoundException(
            "Credit card brand for '$originalBrandId' was not found."
        );
    }

    /**
     * @inheritdoc
     */
    public function append(CreditCardBrandInterface $creditCardBrand): bool
    {
        $brandId = $this->normalizeBrandId($creditCardBrand->getId());
        if (isset($this->brands[$brandId])) {
            return false; // Type already exists, do not add
        }
        $className = get_class($creditCardBrand);
        if (isset(self::$cachedFactoryCards[$className])) {
            self::$cachedFactoryCards[$className] = $creditCardBrand;
        }
        $this->brands[$brandId] = $creditCardBrand;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function prepend(CreditCardBrandInterface $creditCardBrand): bool
    {
        $brandId = $this->normalizeBrandId($creditCardBrand->getId());
        if (isset($this->brands[$brandId])) {
            return false; // Type already exists, do not add
        }
        $className = get_class($creditCardBrand);
        if (isset(self::$cachedFactoryCards[$className])) {
            self::$cachedFactoryCards[$className] = $creditCardBrand;
        }
        // Prepend the new brand to the beginning of the array
        $this->brands = [$brandId => $creditCardBrand] + $this->brands;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function replace(CreditCardBrandInterface $creditCardBrand): ?CreditCardBrandInterface
    {
        $brandId = $this->normalizeBrandId($creditCardBrand->getId());
        $previous = null;
        if (isset($this->brands[$brandId])) {
            $previous = $this->brands[$brandId];
            unset($this->brands[$brandId]); // Remove the existing brand before replacing
            if (is_string($previous)) {
                // mark as core
                $previous = self::createNewOrUse($previous);
            }
        }
        $className = get_class($creditCardBrand);
        if (isset(self::$cachedFactoryCards[$className])) {
            self::$cachedFactoryCards[$className] = $creditCardBrand;
        }
        $this->brands[$brandId] = $creditCardBrand;
        return $previous;
    }

    /**
     * @inheritdoc
     */
    public function remove(string|CreditCardBrandInterface $brandId): ?CreditCardBrandInterface
    {
        // normalize the type to lowercase for consistent key access
        $brandId = $this->normalizeBrandId($brandId);
        if (!isset($this->brands[$brandId])) {
            return null;
        }
        $previous = $this->brands[$brandId];
        unset($this->brands[$brandId]);
        if (is_string($previous)) {
            // mark as core
            $previous = self::createNewOrUse($previous);
        }
        return $previous;
    }

    /**
     * @inheritdoc
     */
    public function guess(string|CardInterface $pan): CreditCardBrandInterface
    {
        $pan = is_string($pan) ? $pan : $pan->getNumber();
        foreach ($this->brands as $key => $brand) {
            $this->brands[$key] = $brand = is_string($brand) ? $this->createNewOrUse($brand) : $brand;
            if ($brand->isValid($pan)) {
                return $brand;
            }
        }
        throw new Exceptions\NotFoundException(
            "No matching credit card brand found for the provided PAN."
        );
    }

    /**
     * @inheritdoc
     */
    public static function mask(
        CardInterface|string $card,
        string               $maskingCharacter = '*'
    ): string {
        $card = is_string($card) ? $card : $card->getNumber();
        $length = strlen($card);
        if ($length <= 4) {
            return $card; // No masking needed for short card numbers
        }
        if ($length <= 8) { // For very short card numbers, show only the last 2 digits
            $visibleStart = 0;
            $visibleEnd = 2;
        } elseif ($length <= 10) {
            $visibleStart = 2;
            $visibleEnd = 2;
        } elseif ($length <= 13) {
            $visibleStart = 4;
            $visibleEnd = 3;
        } else {
            $visibleStart = 4;
            $visibleEnd = 4;
        }
        $numberOfMaskedCharacters = $length - ($visibleStart + $visibleEnd);
        $maskedSection = str_repeat($maskingCharacter, $numberOfMaskedCharacters);
        return substr($card, 0, $visibleStart) . $maskedSection . substr($card, -$visibleEnd);
    }

    public function count(): int
    {
        return count($this->getBrands());
    }
}
