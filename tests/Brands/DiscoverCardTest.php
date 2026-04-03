<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\DiscoverCard;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DiscoverCardTest extends TestCase
{
    private DiscoverCard $discoverCard;

    protected function setUp(): void
    {
        $this->discoverCard = new DiscoverCard();
    }

    #[test]
    public function testDiscoverCardImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->discoverCard);
    }

    #[test]
    public function testDiscoverCardIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->discoverCard->getId());
    }

    #[test]
    public function testDiscoverCardNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->discoverCard->getName());
    }

    #[test]
    public function testDiscoverCardIINListIsValid(): void
    {
        $iinList = $this->discoverCard->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testDiscoverCardPANLengthsAreValid(): void
    {
        $panLengths = $this->discoverCard->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testDiscoverCardCVVLengthIsValid(): void
    {
        $cvvLengths = $this->discoverCard->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testDiscoverCardHasInvalidLength()
    {
        $invalidPan = '1234567890123';
        $this->assertFalse($this->discoverCard->isValid($invalidPan));
    }

    #[test]
    public function testDiscoverCardHasInvalidLuhn()
    {
        $invalidPan = '622164264430421951';
        $this->assertFalse($this->discoverCard->isValid($invalidPan));
    }

    #[test]
    public function testDiscoverCardHasValidCard6011()
    {
        $invalidPan = '6011028304396610839';
        $this->assertTrue($this->discoverCard->isValid($invalidPan));
    }

    #[test]
    public function testDiscoverCardHasValidCard65()
    {
        $invalidPan = '6504147379066902';
        $this->assertTrue($this->discoverCard->isValid($invalidPan));
    }

    #[test]
    public function testDiscoverCardHasValidCard64()
    {
        $invalidPan = '6482392489846399';
        $this->assertTrue($this->discoverCard->isValid($invalidPan));
    }

    #[test]
    public function testDiscoverCardGeneratePAN(): void
    {
        $pan = $this->discoverCard->generate();
        $this->assertTrue($this->discoverCard->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testDiscoverCardGenerateCVV(): void
    {
        $cvv = $this->discoverCard->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
