<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests;

use GatePay\CreditCard\Brands\Mastercard;
use GatePay\CreditCard\Brands\Visa;
use GatePay\CreditCard\Card;
use GatePay\CreditCard\CreditCard;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use DateTime;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

class CardTest extends TestCase
{
    private readonly CreditCard $creditCard;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->creditCard = new CreditCard();
    }

    #[test]
    public function testSetExpiry(): void
    {
        $number = $this->creditCard->get(Visa::ID)->generate();
        $card = new Card($number);
        $expected = '12/2025';
        $expectedShort = '12/25';
        $date = new DateTime('2025-12-01');
        $card->setExpiry($expected);
        $this->assertSame($expected, $card->getExpiry());
        $card->setExpiry($expectedShort);
        $this->assertSame($expected, $card->getExpiry());
        $card->setExpiry($date);
        $this->assertSame($expected, $card->getExpiry());
        $card->setExpiry(null);
        $this->assertSame(null, $card->getExpiry());
        try {
            $card->setExpiry('2025');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('Invalid expiry format. Expected MM/YY or MM/YYYY.', $e->getMessage());
        }
        try {
            // invalid month
            $card->setExpiry('13/2025');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('13', $e->actualType);
            $this->assertSame('Invalid month format. Expected a numeric string between 01 and 12.', $e->getMessage());
        }
        try {
            // invalid month
            $card->setExpiry('0/2025');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('0', $e->actualType);
            $this->assertSame('Invalid month format. Expected a numeric string between 01 and 12.', $e->getMessage());
        }
        try {
            // invalid month
            $card->setExpiry('01/105');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('105', $e->actualType);
            $this->assertSame(
                'Invalid year format. Expected a numeric string between 1000 and 9999.',
                $e->getMessage()
            );
        }
    }

    #[test]
    public function testIsValidCardNumber(): void
    {
        $number = $this->creditCard->get(Visa::class)->generate();
        $card = new Card($number);
        $this->assertTrue($card->isValidCardNumber());
        $cardInvalid = new Card('0234567890123456');
        $this->assertFalse($cardInvalid->isValidCardNumber());

        // reuse the same card with a valid IIN but invalid Luhn checksum
        $this->assertFalse($cardInvalid->isValidCardNumber());
        $cardShort = new Card('4111');
        $this->assertFalse($cardShort->isValidCardNumber());
    }

    #[test]
    public function testGetNumber(): void
    {
        $number = $this->creditCard->get(Visa::class)->generate();
        $card = new Card($number);
        $this->assertSame($number, $card->getNumber());
    }

    #[test]
    public function testGetExpiryYear(): void
    {
        $card = new Card('4111111111111111');
        $card->setExpiry('12/2025');
        $this->assertSame('2025', $card->getExpiryYear());
    }

    #[test]
    public function testGetExpiryMonth(): void
    {
        $card = new Card('4111111111111111');
        $card->setExpiry('12/2025');
        $this->assertSame('12', $card->getExpiryMonth());
    }

    #[test]
    public function testGetCardholderName(): void
    {
        $card = new Card('4111111111111111');
        $name = 'John Doe';
        $card->setCardholderName($name);
        $this->assertSame($name, $card->getCardholderName());
        $card->setCardholderName(null);
        $this->assertSame(null, $card->getCardholderName());
    }

    #[test]
    public function testGetExpiry(): void
    {
        $card = new Card('4111111111111111');
        $expected = '12/2025';
        $card->setExpiry($expected);
        $this->assertSame($expected, $card->getExpiry());
        $card->setExpiry(null);
        $this->assertSame(null, $card->getExpiry());
    }

    #[test]
    public function testGetCvv(): void
    {
        $card = new Card('4111111111111111');
        $this->assertSame(null, $card->getCvv());
        $cvv = '123';
        $card->setCVV($cvv);
        $this->assertSame($cvv, $card->getCvv());
        $card->setCVV(null);
        $this->assertSame(null, $card->getCvv());
        try {
            $card->setCVV('12');
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            $this->assertSame('12', $e->actualType);
            $this->assertSame('Invalid CVV format. Expected a numeric string of 3 or 4 digits.', $e->getMessage());
        }
    }

    #[test]
    public function testSetBrand(): void
    {
        $card = new Card('4111111111111111');
        $brand = $this->creditCard->get(Visa::class);
        $card->setBrand($brand);
        $this->assertSame($brand, $card->getCardBrand());
        $invalidBrand = $this->creditCard->get(Mastercard::class);
        try {
            $card->setBrand($invalidBrand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
            // reuse existing properties to provide more context about the error
            $this->assertSame($brand->getName(), $e->actualType);
            $this->assertSame('The provided brand is not valid for the current card number.', $e->getMessage());
        }
    }

    #[test]
    public function testToString(): void
    {
        $number = $this->creditCard->get(Visa::class)->generate();
        $card = new Card($number);
        $this->assertSame(CreditCard::mask($number), (string)$card);
        $this->assertSame($card->__toString(), (string)$card);
    }

    #[test]
    public function testSetCardholderName(): void
    {
        $card = new Card('4111111111111111');
        $name = 'John Doe';
        $card->setCardholderName($name);
        $this->assertSame($name, $card->getCardholderName());
        $card->setCardholderName(null);
        $this->assertSame(null, $card->getCardholderName());
    }

    #[test]
    public function testSetCVV(): void
    {
        $card = new Card('4111111111111111');
        $cvv = '123';
        $card->setCVV($cvv);
        $this->assertSame($cvv, $card->getCvv());
        $card->setCVV(null);
        $this->assertSame(null, $card->getCvv());
    }

    #[test]
    public function testGetCardBrand(): void
    {
        $number = $this->creditCard->get(Visa::class)->generate();
        $card = new Card($number);
        $brand = $card->getCardBrand();
        $this->assertSame($this->creditCard->get(Visa::class), $brand);
        $cardUnknown = new Card('1234567890123456');
        $this->assertSame(null, $cardUnknown->getCardBrand());
        $card = new Card('1111111111111111');
        $this->assertSame(null, $card->getCardBrand($this->creditCard));
    }
}
