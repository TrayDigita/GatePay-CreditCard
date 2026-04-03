<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Solo;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SoloTest extends TestCase
{
    private Solo $solo;

    protected function setUp(): void
    {
        $this->solo = new Solo();
    }

    #[test]
    public function testSoloImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->solo);
    }

    #[test]
    public function testSoloIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->solo->getId());
    }

    #[test]
    public function testSoloNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->solo->getName());
    }

    #[test]
    public function testSoloIINListIsValid(): void
    {
        $iinList = $this->solo->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testSoloPANLengthsAreValid(): void
    {
        $panLengths = $this->solo->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testSoloPANLengthsAreInvalid(): void
    {
        $this->assertFalse(
            $this->solo->isValid('12345678901234567890') // Invalid PAN length
        );
        $this->assertFalse(
            $this->solo->isValid('123456789012') // Too short
        );
    }

    #[test]
    public function testSoloPANIsValid(): void
    {
        $validPan = '6767332505939661539';
        $this->assertTrue(
            $this->solo->isValid($validPan) // Valid Solo PAN
        );
    }

    #[test]
    public function testSoloPANIsInvalidLuhn(): void
    {
        $validPan = '6767332505939661531';
        $this->assertFalse(
            $this->solo->isValid($validPan) // Valid Solo PAN
        );
    }

    #[test]
    public function testSoloCVVLengthIsValid(): void
    {
        $cvvLengths = $this->solo->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testSoloGeneratePAN(): void
    {
        $pan = $this->solo->generate();
        $this->assertTrue($this->solo->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testSoloGenerateCVV(): void
    {
        $cvv = $this->solo->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
