<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\RuPay;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RuPayTest extends TestCase
{
    private RuPay $ruPay;

    protected function setUp(): void
    {
        $this->ruPay = new RuPay();
    }

    #[test]
    public function testRuPayImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->ruPay);
    }

    #[test]
    public function testRuPayIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->ruPay->getId());
    }

    #[test]
    public function testRuPayNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->ruPay->getName());
    }

    #[test]
    public function testRuPayIINListIsValid(): void
    {
        $iinList = $this->ruPay->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testRuPayPANLengthsAreValid(): void
    {
        $panLengths = $this->ruPay->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testRuPayPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->ruPay->isValid('12345678901234567890') // Invalid PAN length
        );
        $this->assertFalse(
            $this->ruPay->isValid('123456789012') // Invalid PAN length
        );
    }

    #[test]
    public function testRuPayPANIsValid(): void
    {
        $validPan = '3533067811360741'; // Valid RuPay PAN
        $this->assertTrue($this->ruPay->isValid($validPan));
    }

    #[test]
    public function testRuPayPANIsInvalidLuhn(): void
    {
        $validPan = '3533067811360740'; // Valid RuPay PAN
        $this->assertFalse($this->ruPay->isValid($validPan));
    }

    #[test]
    public function testRuPayCVVLengthIsValid(): void
    {
        $cvvLengths = $this->ruPay->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testRuPayGeneratePAN(): void
    {
        $pan = $this->ruPay->generate();
        $this->assertTrue($this->ruPay->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testRuPayGenerateCVV(): void
    {
        $cvv = $this->ruPay->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
