<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Mastercard;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MastercardTest extends TestCase
{
    private Mastercard $mastercard;

    protected function setUp(): void
    {
        $this->mastercard = new Mastercard();
    }

    #[test]
    public function testMastercardImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->mastercard);
    }

    #[test]
    public function testMastercardIdIsLowercase(): void
    {
        $this->assertSame('mastercard', $this->mastercard->getId());
    }

    #[test]
    public function testMastercardNameIsCorrect(): void
    {
        $this->assertSame('Mastercard', $this->mastercard->getName());
    }

    #[test]
    public function testMastercardIINListIsValid(): void
    {
        $iinList = $this->mastercard->getIINList();
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testMastercardPANLengthsAreValid(): void
    {
        $panLengths = $this->mastercard->getPANLengths();
        $this->assertContains(16, $panLengths);
    }

    #[test]
    public function testMastercardCVVLengthIsCorrect(): void
    {
        $cvvLengths = $this->mastercard->getCVVLengths();
        $this->assertContains(3, $cvvLengths);
    }

    #[test]
    public function testMastercardCardTypeIsCredit(): void
    {
        $this->assertSame(CardType::CREDIT, $this->mastercard->getType());
    }

    #[test]
    public function testValidMastercardPAN(): void
    {
        $this->assertTrue($this->mastercard->isValid('5329879707824603'));
    }

    #[test]
    public function testValidMastercardPANWithPrefix51(): void
    {
        $this->assertTrue($this->mastercard->isValid('5105105105105100'));
    }

    #[test]
    public function testValidMastercardPANWithPrefix55(): void
    {
        $this->assertTrue($this->mastercard->isValid('5555555555554444'));
    }

    #[test]
    public function testInvalidMastercardPANWithWrongPrefix(): void
    {
        $this->assertFalse($this->mastercard->isValid('4532015112830366'));
    }

    #[test]
    public function testInvalidMastercardPANWithWrongLength(): void
    {
        $this->assertFalse($this->mastercard->isValid('542523301010344'));
    }

    #[test]
    public function testInvalidMastercardPANFailsLuhnAlgorithm(): void
    {
        $this->assertFalse($this->mastercard->isValid('5425233010103440'));
    }

    #[test]
    public function testGenerateMastercardPAN(): void
    {
        $pan = $this->mastercard->generate();
        $this->assertTrue($this->mastercard->isValid($pan));
    }

    #[test]
    public function testMastercardInvalidPrefix(): void
    {
        $pan = '2721000000000000';
        $this->assertFalse($this->mastercard->isValid($pan));
        $pan = '2220000000000000';
        $this->assertFalse($this->mastercard->isValid($pan));
    }

    #[test]
    public function testGenerateMastercardCVV(): void
    {
        $cvv = $this->mastercard->generateCVV();
        $this->assertMatchesRegularExpression('/^\d{3}$/', $cvv);
    }

    #[test]
    public function testGenerateMastercardPANMultipleTimes(): void
    {
        $pan1 = $this->mastercard->generate();
        $pan2 = $this->mastercard->generate(17);

        $this->assertTrue($this->mastercard->isValid($pan1));
        $this->assertTrue($this->mastercard->isValid($pan2));
        $this->assertNotSame($pan1, $pan2);
    }
}
