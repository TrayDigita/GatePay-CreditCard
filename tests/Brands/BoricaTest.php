<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Borica;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BoricaTest extends TestCase
{
    private Borica $borica;

    protected function setUp(): void
    {
        $this->borica = new Borica();
    }

    #[test]
    public function testBoricaImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->borica);
    }

    #[test]
    public function testBoricaIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->borica->getId());
    }

    #[test]
    public function testBoricaNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->borica->getName());
    }

    #[test]
    public function testBoricaIINListIsValid(): void
    {
        $iinList = $this->borica->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testBoricaPANLengthsAreValid(): void
    {
        $panLengths = $this->borica->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testBoricaCVVLengthIsValid(): void
    {
        $cvvLengths = $this->borica->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testBoricaHasIsInValidLength(): void
    {
        $this->assertFalse(
            $this->borica->isValid('220570771878712')
        );
    }

    #[test]
    public function testBoricaHasIsInValidLuhn(): void
    {
        $this->assertFalse(
            $this->borica->isValid('2205707718787121')
        );
    }

    #[test]
    public function testBoricaGeneratePAN(): void
    {
        $pan = $this->borica->generate();
        $this->assertTrue($this->borica->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testBoricaGenerateCVV(): void
    {
        $cvv = $this->borica->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
