<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\VisaElectron;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VisaElectronTest extends TestCase
{
    private VisaElectron $visaElectron;

    protected function setUp(): void
    {
        $this->visaElectron = new VisaElectron();
    }

    #[test]
    public function testVisaElectronImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->visaElectron);
    }

    #[test]
    public function testVisaElectronIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->visaElectron->getId());
    }

    #[test]
    public function testVisaElectronNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->visaElectron->getName());
    }

    #[test]
    public function testVisaElectronIINListIsValid(): void
    {
        $iinList = $this->visaElectron->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testVisaElectronPANLengthsAreValid(): void
    {
        $panLengths = $this->visaElectron->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testVisaElectronCVVLengthIsValid(): void
    {
        $cvvLengths = $this->visaElectron->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testVisaElectronInvalidLength(): void
    {
        $this->assertFalse(
            $this->visaElectron->isValid('123')
        );
    }

    #[test]
    public function testVisaElectronInvalidLuhn(): void
    {
        $this->assertFalse(
            $this->visaElectron->isValid('4132743188997382')
        );
    }

    #[test]
    public function testVisaElectronGeneratePAN(): void
    {
        $pan = $this->visaElectron->generate();
        $this->assertTrue($this->visaElectron->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testVisaElectronGenerateCVV(): void
    {
        $cvv = $this->visaElectron->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
