<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\MaestroUK;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MaestroUKTest extends TestCase
{
    private MaestroUK $maestroUK;

    protected function setUp(): void
    {
        $this->maestroUK = new MaestroUK();
    }

    #[test]
    public function testMaestroUKImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->maestroUK);
    }

    #[test]
    public function testMaestroUKIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->maestroUK->getId());
    }

    #[test]
    public function testMaestroUKNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->maestroUK->getName());
    }

    #[test]
    public function testMaestroUKIINListIsValid(): void
    {
        $iinList = $this->maestroUK->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testMaestroUKPANLengthsAreValid(): void
    {
        $panLengths = $this->maestroUK->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testMaestroUKPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->maestroUK->isValid('12345678901234567') // 17 digits, which is invalid for Maestro UK
        );
        $this->assertFalse(
            $this->maestroUK->isValid('12345678901234567890') // Too long
        );
    }

    #[test]
    public function testMaestroUKPANIsValid(): void
    {
        $validPan = '6759649826438453'; // A valid Maestro UK PAN
        $this->assertTrue($this->maestroUK->isValid($validPan));
    }

    #[test]
    public function testMaestroUKPANIsInvalidLuhn(): void
    {
        $validPan = '6759649826438451'; // A valid Maestro UK PAN
        $this->assertFalse($this->maestroUK->isValid($validPan));
    }

    #[test]
    public function testMaestroUKCVVLengthIsValid(): void
    {
        $cvvLengths = $this->maestroUK->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testMaestroUKGeneratePAN(): void
    {
        $pan = $this->maestroUK->generate();
        $this->assertTrue($this->maestroUK->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testMaestroUKGenerateCVV(): void
    {
        $cvv = $this->maestroUK->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
