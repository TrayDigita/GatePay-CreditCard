<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\LankaPay;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LankaPayTest extends TestCase
{
    private LankaPay $lankaPay;

    protected function setUp(): void
    {
        $this->lankaPay = new LankaPay();
    }

    #[test]
    public function testLankaPayImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->lankaPay);
    }

    #[test]
    public function testLankaPayIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->lankaPay->getId());
    }

    #[test]
    public function testLankaPayNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->lankaPay->getName());
    }

    #[test]
    public function testLankaPayIINListIsValid(): void
    {
        $iinList = $this->lankaPay->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testLankaPayPANLIsValid(): void
    {
        $validPan = '3571116232203692';
        $this->assertTrue($this->lankaPay->isValid($validPan));
    }

    public function testLankaPayPANIsInvalidLuhn(): void
    {
        $invalidPan = '3571116232203691';
        $this->assertFalse($this->lankaPay->isValid($invalidPan));
    }

    #[test]
    public function testLankaPayPANLengthsAreValid(): void
    {
        $panLengths = $this->lankaPay->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testLankaPayCVVLengthIsValid(): void
    {
        $cvvLengths = $this->lankaPay->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testLankaPayGeneratePAN(): void
    {
        $pan = $this->lankaPay->generate();
        $this->assertTrue($this->lankaPay->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testLankaPayGenerateCVV(): void
    {
        $cvv = $this->lankaPay->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
