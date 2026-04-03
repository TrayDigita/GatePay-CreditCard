<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\DinersClubInternational;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DinersClubInternationalTest extends TestCase
{
    private DinersClubInternational $dinersClubInternational;

    protected function setUp(): void
    {
        $this->dinersClubInternational = new DinersClubInternational();
    }

    #[test]
    public function testDinersClubInternationalImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->dinersClubInternational);
    }

    #[test]
    public function testDinersClubInternationalIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dinersClubInternational->getId());
    }

    #[test]
    public function testDinersClubInternationalNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dinersClubInternational->getName());
    }

    #[test]
    public function testDinersClubInternationalIINListIsValid(): void
    {
        $iinList = $this->dinersClubInternational->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testDinersClubInternationalPANLengthsAreValid(): void
    {
        $panLengths = $this->dinersClubInternational->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testDinersClubInternationalCVVLengthIsValid(): void
    {
        $cvvLengths = $this->dinersClubInternational->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testDinersClubInternationalGeneratePAN(): void
    {
        $pan = $this->dinersClubInternational->generate();
        $this->assertTrue($this->dinersClubInternational->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testDinersClubInternationalHasInvalidLength()
    {
        $invalidPan = '12345678901234567890'; // 20 digits, which is invalid for Diners Club International
        $this->assertFalse($this->dinersClubInternational->isValid($invalidPan));
    }

    #[test]
    public function testDinersClubInternationalGenerateCVV(): void
    {
        $cvv = $this->dinersClubInternational->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
