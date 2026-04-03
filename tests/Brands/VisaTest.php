<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Visa;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VisaTest extends TestCase
{
    private Visa $visa;

    protected function setUp(): void
    {
        $this->visa = new Visa();
    }

    #[test]
    public function testVisaImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->visa);
    }

    #[test]
    public function testVisaIdIsLowercase(): void
    {
        $this->assertSame('visa', $this->visa->getId());
    }

    #[test]
    public function testVisaNameIsCorrect(): void
    {
        $this->assertSame('Visa', $this->visa->getName());
    }

    #[test]
    public function testVisaIINListContains4(): void
    {
        $iinList = $this->visa->getIINList();
        $this->assertContains(4, $iinList);
    }

    #[test]
    public function testVisaPANLengthsAreValid(): void
    {
        $panLengths = $this->visa->getPANLengths();
        $this->assertContains(13, $panLengths);
        $this->assertContains(16, $panLengths);
        $this->assertContains(19, $panLengths);
    }

    #[test]
    public function testVisaCVVLengthIsCorrect(): void
    {
        $cvvLengths = $this->visa->getCVVLengths();
        $this->assertContains(3, $cvvLengths);
    }

    #[test]
    public function testVisaCardTypeIsCredit(): void
    {
        $this->assertSame(CardType::CREDIT, $this->visa->getType());
    }

    #[test]
    public function testValidVisa16DigitPAN(): void
    {
        $this->assertTrue($this->visa->isValid('4532015112830366'));
    }

    #[test]
    public function testValidVisa13DigitPAN(): void
    {
        $this->assertTrue($this->visa->isValid('4222222222222'));
    }

    #[test]
    public function testValidVisa19DigitPAN(): void
    {
        $this->assertFalse($this->visa->isValid('4539111111111111111'));
    }

    #[test]
    public function testInvalidVisaPANWithWrongPrefix(): void
    {
        $this->assertFalse($this->visa->isValid('5532015112830366'));
    }

    #[test]
    public function testInvalidVisaPANWithWrongLength(): void
    {
        $this->assertTrue($this->visa->isValid('4532015112830'));
    }

    #[test]
    public function testInvalidVisaPANFailsLuhnAlgorithm(): void
    {
        $this->assertFalse($this->visa->isValid('4532015112830360'));
    }

    #[test]
    public function testGenerateVisaPAN(): void
    {
        $pan = $this->visa->generate();
        $this->assertTrue($this->visa->isValid($pan));
        $this->assertStringStartsWith('4', $pan);
    }

    #[test]
    public function testVisaInvalidLength(): void
    {
        $this->assertFalse(
            $this->visa->isValid('123')
        );
    }

    #[test]
    public function testGenerateVisaCVV(): void
    {
        $cvv = $this->visa->generateCVV();
        $this->assertMatchesRegularExpression('/^\d{3}$/', $cvv);
    }

    #[test]
    public function testGenerateVisaPANMultipleTimes(): void
    {
        $pan1 = $this->visa->generate();
        $pan2 = $this->visa->generate();

        $this->assertTrue($this->visa->isValid($pan1));
        $this->assertTrue($this->visa->isValid($pan2));
        $this->assertNotSame($pan1, $pan2);
    }
}
