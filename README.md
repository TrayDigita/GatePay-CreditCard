# Credit Card Validator & Generator

[![PHP CI](https://github.com/TrayDigita/GatePay-CreditCard/actions/workflows/ci.yaml/badge.svg)](https://github.com/TrayDigita/CreditCard/actions/workflows/ci.yaml)
[![codecov](https://codecov.io/gh/TrayDigita/GatePay-CreditCard/branch/main/graph/badge.svg)](https://codecov.io/gh/TrayDigita/CreditCard)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)

Zero Dependency Credit Card (And Debit Card) Checker, based on [Luhn Algorithm](https://en.wikipedia.org/wiki/Luhn_algorithm).


## Installation

```bash
composer require "gatepay/credit-card"
```

## Usage

For implementation please refer [HOW TO](HOWTO.md) guide.

## Requirements

`php 8.1` or higher is required to use this library.


## Credit / Debit Card Brands

The brand source IIN (Issuer Identification Number) data is based on [Wikipedia IIN](https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN)).

By default, supported brands (factories) are:

- [American Express](src/Brands/AmericanExpress.php)
- [BankCard](src/Brands/BankCard.php)
- [Borica](src/Brands/Borica.php)
- [China T-Union](src/Brands/ChinaTUnion.php)
- [Dankort](src/Brands/Dankort.php)
- [Diners Club](src/Brands/DinersClub.php)
- [Diners Club International](src/Brands/DinersClubInternational.php)
- [Discover Card](src/Brands/DiscoverCard.php)
- [Elo](src/Brands/Elo.php)
- [Humo](src/Brands/Humo.php)
- [HiperCard](src/Brands/HiperCard.php)
- [InterPayment](src/Brands/InterPayment.php)
- [JCB](src/Brands/JCB.php)
- [LankaPay](src/Brands/LankaPay.php)
- [Laser](src/Brands/Laser.php)
- [Maestro](src/Brands/Maestro.php)
- [Maestro UK](src/Brands/MaestroUK.php)
- [Mastercard](src/Brands/Mastercard.php)
- [Mir](src/Brands/Mir.php)
- [RuPay](src/Brands/RuPay.php)
- [Solo](src/Brands/Solo.php)
- [Switch Debit](src/Brands/SwitchDebit.php)
- [Troy](src/Brands/Troy.php)
- [UnionPay](src/Brands/UnionPay.php)
- [UzCard](src/Brands/UzCard.php)
- [Verve](src/Brands/Verve.php)
- [Visa](src/Brands/Visa.php)
- [Visa Electron](src/Brands/VisaElectron.php)
- [GPN](src/Brands/GPN.php) (Recommended to disable this brand, as it is widely overlapping with other brands, and it is not widely used - disabled by default)


## License

[MIT License](LICENSE) - Use it, modify it, ship it.

---

<!--suppress HtmlDeprecatedAttribute -->
<p align="center" style="text-align: center">
  <b>Stop fighting your payment gateway. Start shipping features.</b>
  <br><br>
  Built with ❤️ for developers who have better things to do than debug payment integrations.
</p>