<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\ChinaTUnion;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ChinaTUnionTest extends TestCase
{
    private ChinaTUnion $chinaTUnion;

    protected function setUp(): void
    {
        $this->chinaTUnion = new ChinaTUnion();
    }

    #[test]
    public function testChinaTUnionImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->chinaTUnion);
    }

    #[test]
    public function testChinaTUnionIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->chinaTUnion->getId());
    }

    #[test]
    public function testChinaTUnionNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->chinaTUnion->getName());
    }

    #[test]
    public function testChinaTUnionIINListIsValid(): void
    {
        $iinList = $this->chinaTUnion->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testChinaTUnionPANLengthsAreValid(): void
    {
        $panLengths = $this->chinaTUnion->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testChinaTUnionCVVLengthIsValid(): void
    {
        $cvvLengths = $this->chinaTUnion->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testChinaTUnionHasInvalidLuhn(): void
    {
        $this->assertFalse($this->chinaTUnion->isValid('3187635026624814951'));
    }

    #[test]
    public function testChinaTUnionGeneratePAN(): void
    {
        $pan = $this->chinaTUnion->generate();
        $this->assertTrue($this->chinaTUnion->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testChinaTUnionGenerateCVV(): void
    {
        $cvv = $this->chinaTUnion->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
