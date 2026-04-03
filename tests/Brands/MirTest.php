<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Mir;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MirTest extends TestCase
{
    private Mir $mir;

    protected function setUp(): void
    {
        $this->mir = new Mir();
    }

    #[test]
    public function testMirImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->mir);
    }

    #[test]
    public function testMirIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->mir->getId());
    }

    #[test]
    public function testMirNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->mir->getName());
    }

    #[test]
    public function testMirIINListIsValid(): void
    {
        $iinList = $this->mir->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testMirPANLengthsAreValid(): void
    {
        $panLengths = $this->mir->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testMirPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->mir->isValid('12345678901234567890') // Too long
        );
        $this->assertFalse(
            $this->mir->isValid('123456789012') // Too short
        );
    }

    #[test]
    public function testMirPanIsValid(): void
    {
        $validPan = '2200154517466818'; // Valid Mir PAN
        $this->assertTrue(
            $this->mir->isValid($validPan) // Valid Mir PAN
        );
    }

    #[test]
    public function testMirPanIsInvalidLun(): void
    {
        $validPan = '2200154517466811'; // Valid Mir PAN
        $this->assertFalse(
            $this->mir->isValid($validPan) // Valid Mir PAN
        );
    }

    #[test]
    public function testMirCVVLengthIsValid(): void
    {
        $cvvLengths = $this->mir->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testMirGeneratePAN(): void
    {
        $pan = $this->mir->generate();
        $this->assertTrue($this->mir->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testMirGenerateCVV(): void
    {
        $cvv = $this->mir->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
