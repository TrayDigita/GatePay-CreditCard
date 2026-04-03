<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Verve;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VerveTest extends TestCase
{
    private Verve $verve;

    protected function setUp(): void
    {
        $this->verve = new Verve();
    }

    #[test]
    public function testVerveImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->verve);
    }

    #[test]
    public function testVerveIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->verve->getId());
    }

    #[test]
    public function testVerveNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->verve->getName());
    }

    #[test]
    public function testVerveIINListIsValid(): void
    {
        $iinList = $this->verve->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testVervePANLengthsAreValid(): void
    {
        $panLengths = $this->verve->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testVervePANInvalidLength(): void
    {
        $this->assertFalse(
            $this->verve->isValid('12345678901234567890') // Invalid length
        );
    }

    #[test]
    public function testVervePANInvalidPan6500(): void
    {
        $this->assertFalse(
            // above 650027 is max IIN for Verve
            $this->verve->isValid('6500291694169240129') // Invalid length
        );
    }

    #[test]
    public function testVervePANInvalidPanLuhn(): void
    {
        $this->assertFalse(
            // above 650027 is max IIN for Verve
            $this->verve->isValid('5061981694169240129') // Invalid length
        );
    }

    #[test]
    public function testVervePANInvalidPan506xx(): void
    {
        $this->assertFalse(
            // above 650027 is max IIN for Verve
            $this->verve->isValid('5061991694169240129') // Invalid length
        );
    }

    #[test]
    public function testVerveCVVLengthIsValid(): void
    {
        $cvvLengths = $this->verve->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testVerveGeneratePAN(): void
    {
        $pan = $this->verve->generate();
        $this->assertTrue($this->verve->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testVerveGenerateCVV(): void
    {
        $cvv = $this->verve->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
