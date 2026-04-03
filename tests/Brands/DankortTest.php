<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Dankort;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DankortTest extends TestCase
{
    private Dankort $dankort;

    protected function setUp(): void
    {
        $this->dankort = new Dankort();
    }

    #[test]
    public function testDankortImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->dankort);
    }

    #[test]
    public function testDankortIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dankort->getId());
    }

    #[test]
    public function testDankortNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dankort->getName());
    }

    #[test]
    public function testDankortIINListIsValid(): void
    {
        $iinList = $this->dankort->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testDankortPANLengthsAreValid(): void
    {
        $panLengths = $this->dankort->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testDankortCVVLengthIsValid(): void
    {
        $cvvLengths = $this->dankort->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testDankortHasInvalidLengths(): void
    {
        $this->assertFalse($this->dankort->isValid('501950517676252'));
    }

    #[test]
    public function testDankortHasInvalidLuhn(): void
    {
        $this->assertFalse($this->dankort->isValid('5019505176762521'));
    }

    #[test]
    public function testDankortGeneratePAN(): void
    {
        $pan = $this->dankort->generate();
        $this->assertTrue($this->dankort->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testDankortGenerateCVV(): void
    {
        $cvv = $this->dankort->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
