<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\InterPayment;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InterPaymentTest extends TestCase
{
    private InterPayment $interPayment;

    protected function setUp(): void
    {
        $this->interPayment = new InterPayment();
    }

    #[test]
    public function testInterPaymentImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->interPayment);
    }

    #[test]
    public function testInterPaymentIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->interPayment->getId());
    }

    #[test]
    public function testInterPaymentNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->interPayment->getName());
    }

    #[test]
    public function testInterPaymentIINListIsValid(): void
    {
        $iinList = $this->interPayment->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testInterPaymentPANLengthsAreValid(): void
    {
        $panLengths = $this->interPayment->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testInterPaymentCVVLengthIsValid(): void
    {
        $cvvLengths = $this->interPayment->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testInterPaymentPanLengthIsInValid(): void
    {
        $this->assertFalse($this->interPayment->isValid('12345678901234567890'));
    }

    #[test]
    public function testInterPaymentPanIsValid(): void
    {
        $this->assertTrue($this->interPayment->isValid('6367475308735255'));
    }

    #[test]
    public function testInterPaymentPanIsInvalidLuhn(): void
    {
        $this->assertFalse($this->interPayment->isValid('6367475308735251'));
    }

    #[test]
    public function testInterPaymentGeneratePAN(): void
    {
        $pan = $this->interPayment->generate();
        $this->assertTrue($this->interPayment->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testInterPaymentGenerateCVV(): void
    {
        $cvv = $this->interPayment->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
