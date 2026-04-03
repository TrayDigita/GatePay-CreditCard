<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests;

use GatePay\CreditCard\CardType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

class CardTypeTest extends TestCase
{
    #[test]
    public function testCardTypeDebitValue(): void
    {
        self::assertSame(1, CardType::DEBIT->value);
    }

    #[test]
    public function testCardTypeCreditValue(): void
    {
        self::assertSame(2, CardType::CREDIT->value);
    }

    #[test]
    public function testCardTypeDebitName(): void
    {
        self::assertSame('DEBIT', CardType::DEBIT->name);
    }

    #[test]
    public function testCardTypeCreditName(): void
    {
        self::assertSame('CREDIT', CardType::CREDIT->name);
    }

    #[test]
    public function testCardTypeCanBeInstantiatedFromValue(): void
    {
        $debit = CardType::tryFrom(1);
        $credit = CardType::tryFrom(2);

        self::assertSame(CardType::DEBIT, $debit);
        self::assertSame(CardType::CREDIT, $credit);
    }

    #[test]
    public function testCardTypeReturnsNullForInvalidValue(): void
    {
        /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
        $invalid = CardType::tryFrom(999);
        self::assertNull($invalid);
    }

    #[test]
    public function testCardTypeFromValueThrowsExceptionForInvalidValue(): void
    {
        self::expectException(ValueError::class);
        /** @noinspection PhpExpressionResultUnusedInspection */
        /** @noinspection PhpCaseWithValueNotFoundInEnumInspection */
        CardType::from(999);
    }

    #[test]
    public function testCardTypeCases(): void
    {
        $cases = CardType::cases();
        self::assertCount(2, $cases);
        self::assertContains(CardType::DEBIT, $cases);
        self::assertContains(CardType::CREDIT, $cases);
    }
}
