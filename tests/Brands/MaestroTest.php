<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Maestro;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MaestroTest extends TestCase
{
    private Maestro $maestro;

    protected function setUp(): void
    {
        $this->maestro = new Maestro();
    }

    #[test]
    public function testMaestroImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->maestro);
    }

    #[test]
    public function testMaestroIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->maestro->getId());
    }

    #[test]
    public function testMaestroNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->maestro->getName());
    }

    #[test]
    public function testMaestroIINListIsValid(): void
    {
        $iinList = $this->maestro->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testMaestroPANLengthsAreValid(): void
    {
        $panLengths = $this->maestro->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testMaestroPANLengthsIsInvalid(): void
    {
        $this->assertFalse(
            $this->maestro->isValid('123456789012') // Too short
        );
        $this->assertFalse(
            $this->maestro->isValid('12345678901234567890') // Too long
        );
    }

    #[test]
    public function testMaestroPANIsValid(): void
    {
        $validPan = '5020199608134909';
        $this->assertTrue(
            $this->maestro->isValid($validPan) // Valid Maestro PAN
        );
    }

    #[test]
    public function testMaestroPANIsInvalidLuhn(): void
    {
        $inValidPan = '5020199608134901';
        $this->assertFalse(
            $this->maestro->isValid($inValidPan) // Valid Maestro PAN
        );
    }

    #[test]
    public function testMaestroCVVLengthIsValid(): void
    {
        $cvvLengths = $this->maestro->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testMaestroGeneratePAN(): void
    {
        $pan = $this->maestro->generate();
        $this->assertTrue($this->maestro->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testMaestroGenerateCVV(): void
    {
        $cvv = $this->maestro->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
