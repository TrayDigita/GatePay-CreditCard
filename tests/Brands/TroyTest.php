<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Troy;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TroyTest extends TestCase
{
    private Troy $troy;

    protected function setUp(): void
    {
        $this->troy = new Troy();
    }

    #[test]
    public function testTroyImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->troy);
    }

    #[test]
    public function testTroyIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->troy->getId());
    }

    #[test]
    public function testTroyNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->troy->getName());
    }

    #[test]
    public function testTroyIINListIsValid(): void
    {
        $iinList = $this->troy->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testTroyPANLengthsAreValid(): void
    {
        $panLengths = $this->troy->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testTroyPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->troy->isValid('12345678901234567890')
        );
        $this->assertFalse(
            $this->troy->isValid('123456789012') // Too short
        );
    }

    #[test]
    public function testTroyPANIsValid(): void
    {
        $validPan = '6531342908797310';
        $this->assertTrue($this->troy->isValid($validPan));
    }

    #[test]
    public function testTroyPANIsInvalidLuhn(): void
    {
        $validPan = '6531342908797311';
        $this->assertFalse($this->troy->isValid($validPan));
    }

    #[test]
    public function testTroyCVVLengthIsValid(): void
    {
        $cvvLengths = $this->troy->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testTroyGeneratePAN(): void
    {
        $pan = $this->troy->generate();
        $this->assertTrue($this->troy->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testTroyGenerateCVV(): void
    {
        $cvv = $this->troy->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
