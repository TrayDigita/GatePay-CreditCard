<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\BankCard;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BankCardTest extends TestCase
{
    private BankCard $bankCard;

    protected function setUp(): void
    {
        $this->bankCard = new BankCard();
    }

    #[test]
    public function testBankCardImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->bankCard);
    }

    #[test]
    public function testBankCardIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->bankCard->getId());
    }

    #[test]
    public function testBankCardNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->bankCard->getName());
    }

    #[test]
    public function testBankCardIINListIsValid(): void
    {
        $iinList = $this->bankCard->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testBankCardPANLengthsAreValid(): void
    {
        $panLengths = $this->bankCard->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
        foreach ($panLengths as $length) {
            $this->assertGreaterThanOrEqual(8, $length);
            $this->assertLessThanOrEqual(19, $length);
        }
    }

    #[test]
    public function testBankCardCVVLengthIsValid(): void
    {
        $cvvLengths = $this->bankCard->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
        foreach ($cvvLengths as $length) {
            $this->assertGreaterThanOrEqual(3, $length);
            $this->assertLessThanOrEqual(4, $length);
        }
    }

    #[test]
    public function testBankCardHasValidCardType(): void
    {
        $type = $this->bankCard->getType();
        $this->assertNotNull($type);
    }

    #[test]
    public function testBankCardGeneratePAN(): void
    {
        $pan = $this->bankCard->generate();
        $this->assertTrue($this->bankCard->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testBankCardHasInValidLength(): void
    {
        $this->assertFalse(
            $this->bankCard->isValid('1234567') // 7 digits, less than minimum length
        );
    }

    public function testBankCardHasInvalidPrefix(): void
    {
        $this->assertFalse(
            $this->bankCard->isValid('9999999999999999') // Invalid prefix
        );
    }

    public function testBankCardHasInvalidLuhn(): void
    {
        $this->assertFalse(
            $this->bankCard->isValid('5610676880217051') // Invalid prefix
        );
    }

    public function testBankCardHasInvalidCard(): void
    {
        $this->assertFalse(
            $this->bankCard->isValid('5611676880217051') // Invalid prefix
        );
    }

    #[test]
    public function testBankCardGenerateCVV(): void
    {
        $cvv = $this->bankCard->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
