# HOW TO

## Installation

Run:

```bash
composer require gatepay/credit-card
```

## Quick Overview

Main components:

- Brand registry: [`GatePay\CreditCard\CreditCard`](src/CreditCard.php)
- Brand contract: [`GatePay\CreditCard\Interfaces\CreditCardBrandInterface`](src/Interfaces/CreditCardInterface.php)
- Base class for custom brands: [`GatePay\CreditCard\Abstracts\AbstractCreditCardBrand`](src/Abstracts/AbstractCreditCardBrand.php)
- Luhn validator: [`GatePay\CreditCard\Algorithms\Luhn`](src/Algorithms/Luhn.php)

## Basic Usage

Use the registry, fetch a brand, validate a PAN, detect brand, and generate values.

```php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use GatePay\CreditCard\Brands\GPN;
use GatePay\CreditCard\CreditCard;
use GatePay\CreditCard\Exceptions\NotFoundException;

// optionally disable some factory brands by passing their IDs to constructor
// this can make faster detection if you know you won't use some brands,
// and also can be useful to avoid overlapping brands (e.g. GPN)
$disabledFactory = [
    // Verve::ID, // maybe need disabled?
    // GPN::ID // v1.1.1 GPN disabled by default, so no need to disable it explicitly
];
// or maybe want to disable all factory registration
// $disabledFactory = array_keys(CreditCard::FACTORY_CARDS);
$cards = new CreditCard(...$disabledFactory);

// need GPN (Gerbang Pembayaran Nasional) brand? you can add it back by appending or prepending
// $cards->append(new GPN());
// or use prioritize first by prepending
// $cards->prepend(new GPN()); // but don't use this!

// Get a specific brand
$visa = $cards->get('visa');

$pan = '4532015112830366';

echo $visa->getName() . PHP_EOL;                 // Visa
echo $visa->isValid($pan) ? 'valid' : 'invalid'; // valid
echo PHP_EOL;

// Guess brand from PAN
try {
    $guessed = $cards->guess($pan);
    echo $guessed->getId() . PHP_EOL;            // visa
    echo $guessed->getName() . PHP_EOL;          // Visa
} catch (NotFoundException $e) {
    echo 'Brand not found: ' . $e->getMessage() . PHP_EOL;
}

// Generate valid PAN and CVV
$generatedPan = $visa->generate();
$generatedCvv = $visa->generateCVV();

echo $generatedPan . PHP_EOL;
echo $generatedCvv . PHP_EOL;

```

## Important Methods in CreditCard

`CreditCard` provides:

- `has(string $brandId): bool`
- `get(string $brandId): CreditCardBrandInterface`
- `getBrands(): array`
- `guess(string $pan): CreditCardBrandInterface`
- `append(CreditCardBrandInterface $brand): bool`
- `prepend(CreditCardBrandInterface $brand): bool`
- `replace(CreditCardBrandInterface $brand): ?CreditCardBrandInterface`
- `remove(string|CreditCardBrandInterface $brandId): ?CreditCardBrandInterface`

Example:

```php
<?php
declare(strict_types=1);

use GatePay\CreditCard\CreditCard;
use GatePay\CreditCard\Brands\Visa;

// by default factory (core) brands are exists
$cards = new CreditCard();

var_dump($cards->has(Visa::ID));   // true
var_dump($cards->has('unknown'));  // false

$allBrands = $cards->getBrands();
echo count($allBrands) . PHP_EOL;

```

## Creating a Custom Brand

The easiest approach is extending [`AbstractCreditCardBrand`](src/Abstracts/AbstractCreditCardBrand.php).

```php
<?php
declare(strict_types=1);

namespace App\CardBrands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use GatePay\CreditCard\CardType;

final class MyTestCard extends AbstractCreditCardBrand
{
    // Must be unique and lowercase
    protected string $id = 'mytestcard';

    // Display name
    protected string $name = 'My Test Card';

    // Valid IIN/BIN prefixes for this brand
    protected array $iinList = [666, 555];

    // Valid PAN lengths (8..19)
    protected array $panLengths = [16];

    // Valid CVV lengths (usually 3, optionally 4)
    protected array $cvvLength = [3];

    // Optional card type
    protected CardType $cardType = CardType::DEBIT;
}

```

## Registering a Custom Brand

After creating the class, register it in `CreditCard`.

```php
<?php
declare(strict_types=1);

use GatePay\CreditCard\CreditCard;
use App\CardBrands\MyTestCard;

$cards = new CreditCard();

$added = $cards->append(new MyTestCard());
var_dump($added); // true if successfully added

// prepending to make sure the custom class at the beginning of collections
$added = $cards->prepend(new MyCustomClass());
var_dump($added); // true if successfully added

$myBrand = $cards->get('mytestcard');
$newPan = $myBrand->generate(16);

var_dump($myBrand->isValid($newPan)); // true

```

## Notes About replace() and remove()

Removing brand object (even factory)

Example:

```php
<?php
declare(strict_types=1);

use GatePay\CreditCard\CreditCard;
use GatePay\CreditCard\Brands\Visa;

$cards = new CreditCard();

// replacing core factory (Visa) always exists when definition not disabled
$replaceCore = $cards->replace(new Visa());
var_dump($replaceCore); // object Visa()

$removeCore = $cards->remove('visa');
var_dump($removeCore);  // null

```

For custom brands, `replace()` and `remove()` work as expected.

## Direct Luhn Validation

If you only need Luhn validation:

```php
<?php

use GatePay\CreditCard\Algorithms\Luhn;

$number = '4532015112830366';

try {
    Luhn::assert($number);
    echo "valid\n";
} catch (\Throwable $e) {
    echo "invalid: " . $e->getMessage() . "\n";
}

```

## Common Exceptions

- `GatePay\CreditCard\Exceptions\NotFoundException`
  Thrown when a brand cannot be found (`get()` / `guess()`).

- Luhn-related exceptions
  Thrown when input is malformed or checksum validation fails.

## Practical Tips

- Keep custom brand IDs lowercase (e.g., `visa`, `mytestcard`).
- Ensure `iinList` and `panLengths` match your brand rules.
- If your rules are special, override `isValid()` in your custom brand class.