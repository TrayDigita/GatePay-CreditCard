<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Laser;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LaserTest extends TestCase
{
    private Laser $laser;

    protected function setUp(): void
    {
        $this->laser = new Laser();
    }

    #[test]
    public function testLaserImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->laser);
    }

    #[test]
    public function testLaserIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->laser->getId());
    }

    #[test]
    public function testLaserNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->laser->getName());
    }

    #[test]
    public function testLaserIINListIsValid(): void
    {
        $iinList = $this->laser->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testLaserPANLengthsAreValid(): void
    {
        $panLengths = $this->laser->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testLaserPanLengthIsInValid(): void
    {
        $this->assertFalse(
            $this->laser->isValid('12345678901234567890') // Too long
        );
        $this->assertFalse(
            $this->laser->isValid('123456789012') // Too short
        );
    }

    #[test]
    public function testLaserPanIsValid(): void
    {
        $validPan = '6304004627461061786';
        $this->assertTrue($this->laser->isValid($validPan));
    }

    #[test]
    public function testLaserPanIsInValidLuhn(): void
    {
        $validPan = '6304004627461061781';
        $this->assertFalse($this->laser->isValid($validPan));
    }

    #[test]
    public function testLaserCVVLengthIsValid(): void
    {
        $cvvLengths = $this->laser->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testLaserGeneratePAN(): void
    {
        $pan = $this->laser->generate();
        $this->assertTrue($this->laser->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testLaserGenerateCVV(): void
    {
        $cvv = $this->laser->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
