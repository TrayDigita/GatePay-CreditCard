<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\GPN;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GPNTest extends TestCase
{
    private GPN $gpn;

    protected function setUp(): void
    {
        $this->gpn = new GPN();
    }

    #[test]
    public function testGPNImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->gpn);
    }

    #[test]
    public function testGPNIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->gpn->getId());
    }

    #[test]
    public function testGPNNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->gpn->getName());
    }

    #[test]
    public function testGPNIINListIsValid(): void
    {
        $iinList = $this->gpn->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testGPNPANLengthsAreValid(): void
    {
        $panLengths = $this->gpn->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testGPNCVVLengthIsValid(): void
    {
        $cvvLengths = $this->gpn->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testGPNHasInvalidLengths(): void
    {
        $this->assertFalse($this->gpn->isValid('123'));
        $this->assertFalse($this->gpn->isValid('1234567890123456'));
    }

    #[test]
    public function testGPNHasInvalid5(): void
    {
        $this->assertFalse($this->gpn->isValid('5123456789012345'));
    }

    #[test]
    public function testGPNHasIsValid(): void
    {
        $this->assertFalse($this->gpn->isValid('6483959530901651613'));
    }

    #[test]
    public function testGPNHasInvalid6(): void
    {
        $this->assertFalse($this->gpn->isValid('6483959530901651613'));
    }

    #[test]
    public function testGPNHasInvalidLun(): void
    {
        $this->assertFalse($this->gpn->isValid('6383959530901651611'));
    }

    #[test]
    public function testGPNGeneratePAN(): void
    {
        $pan = $this->gpn->generate();
        $this->assertTrue($this->gpn->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testGPNGenerateCVV(): void
    {
        $cvv = $this->gpn->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
