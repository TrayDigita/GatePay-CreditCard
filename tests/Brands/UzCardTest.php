<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\UzCard;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UzCardTest extends TestCase
{
    private UzCard $uzCard;

    protected function setUp(): void
    {
        $this->uzCard = new UzCard();
    }

    #[test]
    public function testUzCardImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->uzCard);
    }

    #[test]
    public function testUzCardIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->uzCard->getId());
    }

    #[test]
    public function testUzCardNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->uzCard->getName());
    }

    #[test]
    public function testUzCardIINListIsValid(): void
    {
        $iinList = $this->uzCard->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testUzCardPANLengthsAreValid(): void
    {
        $panLengths = $this->uzCard->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testUzCardInvalidPanLength(): void
    {
        $this->assertFalse(
            $this->uzCard->isValid('12345678901234567890') // Invalid length
        );
    }

    #[test]
    public function testUzCardIsValidPan(): void
    {
        $this->assertTrue(
            $this->uzCard->isValid('5614502312147913') // Invalid length
        );
    }

    #[test]
    public function testUzCardInvalidLuhn(): void
    {
        $this->assertFalse(
            $this->uzCard->isValid('5614502312147911') // Invalid length
        );
    }

    #[test]
    public function testUzCardCVVLengthIsValid(): void
    {
        $cvvLengths = $this->uzCard->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testUzCardGeneratePAN(): void
    {
        $pan = $this->uzCard->generate();
        $this->assertTrue($this->uzCard->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testUzCardGenerateCVV(): void
    {
        $cvv = $this->uzCard->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
