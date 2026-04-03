<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\SwitchDebit;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SwitchDebitTest extends TestCase
{
    private SwitchDebit $switchDebit;

    protected function setUp(): void
    {
        $this->switchDebit = new SwitchDebit();
    }

    #[test]
    public function testSwitchDebitImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->switchDebit);
    }

    #[test]
    public function testSwitchDebitIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->switchDebit->getId());
    }

    #[test]
    public function testSwitchDebitNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->switchDebit->getName());
    }

    #[test]
    public function testSwitchDebitIINListIsValid(): void
    {
        $iinList = $this->switchDebit->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testSwitchDebitPANLengthsAreValid(): void
    {
        $panLengths = $this->switchDebit->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testSwitchDebitPANLengthsAreInValid(): void
    {
        $this->assertFalse(
            $this->switchDebit->isValid('12345678901234567890') // Invalid PAN length
        );
        $this->assertFalse(
            $this->switchDebit->isValid('123456789012') // Too short
        );
    }

    #[test]
    public function testSwitchDebitPANIsValid(): void
    {
        $validPan = '490371801788289358';
        $this->assertTrue(
            $this->switchDebit->isValid($validPan) // Valid Switch Debit PAN
        );
    }

    #[test]
    public function testSwitchDebitPANIsValid6(): void
    {
        $validPan = '690371801788289358';
        $this->assertFalse(
            $this->switchDebit->isValid($validPan) // Valid Switch Debit PAN
        );
    }

    #[test]
    public function testSwitchDebitPANIsInvalid6(): void
    {
        $validPan = '6760708719538089';
        $this->assertFalse(
            $this->switchDebit->isValid($validPan) // Valid Switch Debit PAN
        );
    }

    #[test]
    public function testSwitchDebitPANIsValidLuhn(): void
    {
        $validPan = '490371801788289351';
        $this->assertFalse(
            $this->switchDebit->isValid($validPan) // Valid Switch Debit PAN
        );
    }

    #[test]
    public function testSwitchDebitCVVLengthIsValid(): void
    {
        $cvvLengths = $this->switchDebit->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testSwitchDebitGeneratePAN(): void
    {
        $pan = $this->switchDebit->generate();
        $this->assertTrue($this->switchDebit->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testSwitchDebitGenerateCVV(): void
    {
        $cvv = $this->switchDebit->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
