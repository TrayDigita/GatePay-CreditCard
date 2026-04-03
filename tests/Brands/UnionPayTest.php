<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\UnionPay;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UnionPayTest extends TestCase
{
    private UnionPay $unionPay;

    protected function setUp(): void
    {
        $this->unionPay = new UnionPay();
    }

    #[test]
    public function testUnionPayImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->unionPay);
    }

    #[test]
    public function testUnionPayIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->unionPay->getId());
    }

    #[test]
    public function testUnionPayNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->unionPay->getName());
    }

    #[test]
    public function testUnionPayIINListIsValid(): void
    {
        $iinList = $this->unionPay->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testUnionPayPANLengthsAreValid(): void
    {
        $panLengths = $this->unionPay->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testUnionPayPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->unionPay->isValid('12345678901234567890') // Invalid PAN length
        );
        $this->assertFalse(
            $this->unionPay->isValid('1234567890123456789012345678901234567890') // Invalid PAN length
        );
    }

    #[test]
    public function testUnionPayPANIsValid(): void
    {
        $validPan = '62347322642924047';
        $this->assertTrue($this->unionPay->isValid($validPan));
    }

    #[test]
    public function testUnionPayPANIsInvalidLuhn(): void
    {
        $validPan = '62347322642924048'; // Invalid Luhn check digit
        $this->assertFalse($this->unionPay->isValid($validPan));
    }

    #[test]
    public function testUnionPayCVVLengthIsValid(): void
    {
        $cvvLengths = $this->unionPay->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testUnionPayGeneratePAN(): void
    {
        $pan = $this->unionPay->generate();
        $this->assertTrue($this->unionPay->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testUnionPayGenerateCVV(): void
    {
        $cvv = $this->unionPay->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
