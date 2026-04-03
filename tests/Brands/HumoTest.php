<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Humo;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HumoTest extends TestCase
{
    private Humo $humo;

    protected function setUp(): void
    {
        $this->humo = new Humo();
    }

    #[test]
    public function testHumoImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->humo);
    }

    #[test]
    public function testHumoIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->humo->getId());
    }

    #[test]
    public function testHumoNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->humo->getName());
    }

    #[test]
    public function testHumoIINListIsValid(): void
    {
        $iinList = $this->humo->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testHumoPANLengthsAreValid(): void
    {
        $panLengths = $this->humo->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testHumoPanLengthInvalid(): void
    {
        $this->assertFalse(
            $this->humo->isValid('12345678901234567890') // Invalid length
        );
    }

    #[test]
    public function testHumoPanIsValid(): void
    {
        $this->assertTrue(
            $this->humo->isValid('9860617475734738') // Invalid length
        );
    }

    #[test]
    public function testHumoPanInvalidLuhn(): void
    {
        $this->assertFalse(
            $this->humo->isValid('9860617475734739') // Invalid length
        );
    }

    #[test]
    public function testHumoCVVLengthIsValid(): void
    {
        $cvvLengths = $this->humo->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testHumoGeneratePAN(): void
    {
        $pan = $this->humo->generate();
        $this->assertTrue($this->humo->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testHumoGenerateCVV(): void
    {
        $cvv = $this->humo->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
