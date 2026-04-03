<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\AmericanExpress;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AmericanExpressTest extends TestCase
{
    private AmericanExpress $amex;

    protected function setUp(): void
    {
        $this->amex = new AmericanExpress();
    }

    #[test]
    public function testAmexImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->amex);
    }

    #[test]
    public function testAmexIdIsLowercase(): void
    {
        $this->assertSame('amex', $this->amex->getId());
    }

    #[test]
    public function testAmexNameIsCorrect(): void
    {
        $this->assertSame('American Express', $this->amex->getName());
    }

    #[test]
    public function testAmexIINListContains34And37(): void
    {
        $iinList = $this->amex->getIINList();
        $this->assertContains(34, $iinList);
        $this->assertContains(37, $iinList);
    }

    #[test]
    public function testAmexPANLengthsAreValid(): void
    {
        $panLengths = $this->amex->getPANLengths();
        $this->assertContains(15, $panLengths);
    }

    #[test]
    public function testAmexCVVLengthIsCorrect(): void
    {
        $cvvLengths = $this->amex->getCVVLengths();
        $this->assertContains(4, $cvvLengths);
    }

    #[test]
    public function testAmexCardTypeIsCredit(): void
    {
        $this->assertSame(CardType::CREDIT, $this->amex->getType());
    }

    #[test]
    public function testValidAmexPANWith34Prefix(): void
    {
        $this->assertTrue($this->amex->isValid('378282246310005'));
    }

    #[test]
    public function testValidAmexPANWith37Prefix(): void
    {
        $this->assertTrue($this->amex->isValid('376449047333005'));
    }

    #[test]
    public function testInvalidAmexPANWithWrongPrefix(): void
    {
        $this->assertFalse($this->amex->isValid('453201511283036'));
    }

    #[test]
    public function testInvalidAmexPANWithWrongLength(): void
    {
        $this->assertFalse($this->amex->isValid('378282246310'));
    }

    #[test]
    public function testInvalidAmexPANFailsLuhnAlgorithm(): void
    {
        $this->assertFalse($this->amex->isValid('378282246310006'));
    }

    #[test]
    public function testGenerateAmexPAN(): void
    {
        $pan = $this->amex->generate();
        $this->assertTrue($this->amex->isValid($pan));
    }

    #[test]
    public function testGenerateAmexCVV(): void
    {
        $cvv = $this->amex->generateCVV();
        $this->assertMatchesRegularExpression('/^\d{4}$/', $cvv);
    }

    #[test]
    public function testGenerateAmexPANMultipleTimes(): void
    {
        $pan1 = $this->amex->generate();
        $pan2 = $this->amex->generate();

        $this->assertTrue($this->amex->isValid($pan1));
        $this->assertTrue($this->amex->isValid($pan2));
        $this->assertNotSame($pan1, $pan2);
    }
}
