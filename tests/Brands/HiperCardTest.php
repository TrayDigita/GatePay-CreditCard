<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\HiperCard;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HiperCardTest extends TestCase
{
    private HiperCard $hiperCard;

    protected function setUp(): void
    {
        $this->hiperCard = new HiperCard();
    }

    #[test]
    public function testHiperCardImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->hiperCard);
    }

    #[test]
    public function testHiperCardIdIsLowercase(): void
    {
        $this->assertSame('hipercard', $this->hiperCard->getId());
    }

    #[test]
    public function testHiperCardNameIsCorrect(): void
    {
        $this->assertSame('HiperCard', $this->hiperCard->getName());
    }

    #[test]
    public function testHiperCardIINListContainsKnownPrefixes(): void
    {
        $iinList = $this->hiperCard->getIINList();

        $this->assertContains(384100, $iinList);
        $this->assertContains(384140, $iinList);
        $this->assertContains(384160, $iinList);
        $this->assertContains(606282, $iinList);
        $this->assertContains(637095, $iinList);
        $this->assertContains(637568, $iinList);
        $this->assertContains(637599, $iinList);
        $this->assertContains(637609, $iinList);
        $this->assertContains(637612, $iinList);
    }

    #[test]
    public function testHiperCardPanLengthIs16Digits(): void
    {
        $this->assertSame([16], $this->hiperCard->getPANLengths());
    }

    #[test]
    public function testHiperCardCVVLengthIs3Digits(): void
    {
        $this->assertSame([3], $this->hiperCard->getCVVLengths());
    }

    #[test]
    public function testHiperCardCardTypeIsCredit(): void
    {
        $this->assertSame(CardType::CREDIT, $this->hiperCard->getType());
    }

    #[test]
    public function testValidHiperCardPANWithPrefix384100(): void
    {
        $this->assertTrue($this->hiperCard->isValid('3841005260181595'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix384140(): void
    {
        $this->assertTrue($this->hiperCard->isValid('3841400830166137'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix384160(): void
    {
        $this->assertTrue($this->hiperCard->isValid('3841601860913906'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix606282(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6062829960308249'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix637095(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6370956281948218'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix637568(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6375689935181903'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix637599(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6375999378657974'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix637609(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6376095432319484'));
    }

    #[test]
    public function testValidHiperCardPANWithPrefix637612(): void
    {
        $this->assertTrue($this->hiperCard->isValid('6376127574911867'));
    }

    #[test]
    public function testInvalidHiperCardPANWithWrongPrefix(): void
    {
        $this->assertFalse($this->hiperCard->isValid('4532015112830366'));
        $this->assertFalse($this->hiperCard->isValid('3841015260181595'));
        $this->assertFalse($this->hiperCard->isValid('6376117574911867'));
    }

    #[test]
    public function testInvalidHiperCardPANWithWrongLength(): void
    {
        $this->assertFalse($this->hiperCard->isValid('637612757491186'));
        $this->assertFalse($this->hiperCard->isValid('63761275749118670'));
    }

    #[test]
    public function testInvalidHiperCardPANFailsLuhnAlgorithm(): void
    {
        $this->assertFalse($this->hiperCard->isValid('6376127574911860'));
    }

    #[test]
    public function testGenerateHiperCardPANUsesConfiguredLengthAndPrefix(): void
    {
        $pan = $this->hiperCard->generate();
        $iinList = $this->hiperCard->getIINList();

        $this->assertMatchesRegularExpression('/^\d{16}$/', $pan);
        $this->assertContains((int)substr($pan, 0, 6), $iinList);
        $this->assertTrue($this->hiperCard->isValid($pan));
    }

    #[test]
    public function testGenerateHiperCardPANNearestLengthStillUses16Digits(): void
    {
        $pan = $this->hiperCard->generate(19);

        $this->assertMatchesRegularExpression('/^\d{16}$/', $pan);
        $this->assertTrue($this->hiperCard->isValid($pan));
    }

    #[test]
    public function testGenerateHiperCardCVV(): void
    {
        $cvv = $this->hiperCard->generateCVV();

        $this->assertMatchesRegularExpression('/^\d{3}$/', $cvv);
    }
}
