<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\Elo;
use GatePay\CreditCard\CardType;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EloTest extends TestCase
{
    private Elo $elo;

    protected function setUp(): void
    {
        $this->elo = new Elo();
    }

    #[test]
    public function testEloImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->elo);
    }

    #[test]
    public function testEloIdIsLowercase(): void
    {
        $this->assertSame('elo', $this->elo->getId());
    }

    #[test]
    public function testEloNameIsCorrect(): void
    {
        $this->assertSame('Elo', $this->elo->getName());
    }

    #[test]
    public function testEloIINListContainsKnownPrefixes(): void
    {
        $iinList = $this->elo->getIINList();

        $this->assertContains(504175, $iinList);
        $this->assertContains(509000, $iinList);
        $this->assertContains(636297, $iinList);
        $this->assertContains(636368, $iinList);
    }

    #[test]
    public function testEloPanLengthIs16Digits(): void
    {
        $this->assertSame([16], $this->elo->getPANLengths());
    }

    #[test]
    public function testEloCVVLengthIs3Digits(): void
    {
        $this->assertSame([3], $this->elo->getCVVLengths());
    }

    #[test]
    public function testEloCardTypeIsCredit(): void
    {
        $this->assertSame(CardType::CREDIT, $this->elo->getType());
    }

    #[test]
    public function testValidEloPANWithPrefix504175(): void
    {
        $this->assertTrue($this->elo->isValid('5041751474369871'));
    }

    #[test]
    public function testValidEloPANWithPrefix509000(): void
    {
        $this->assertTrue($this->elo->isValid('5090007356875158'));
    }

    #[test]
    public function testValidEloPANWithPrefix636297(): void
    {
        $this->assertTrue($this->elo->isValid('6362972497206572'));
    }

    #[test]
    public function testValidEloPANWithPrefix636368(): void
    {
        $this->assertTrue($this->elo->isValid('6363684497535939'));
    }

    #[test]
    public function testInvalidEloPANWithWrongPrefix(): void
    {
        $this->assertFalse($this->elo->isValid('4532015112830366'));
    }

    #[test]
    public function testInvalidEloPANWithWrongLength(): void
    {
        $this->assertFalse($this->elo->isValid('636297249720657'));
    }

    #[test]
    public function testInvalidEloPANFailsLuhnAlgorithm(): void
    {
        $this->assertFalse($this->elo->isValid('5041751474369870'));
    }

    #[test]
    public function testInvalidEloPANFor509BranchOutsideAcceptedSubrange(): void
    {
        $this->assertFalse($this->elo->isValid('5090917286709824'));
    }

    #[test]
    public function testInvalidEloPANForConfigured506Prefix(): void
    {
        $this->assertFalse($this->elo->isValid('5066993918602422'));
    }

    #[test]
    public function testInvalidEloPANForConfiguredPrefix(): void
    {
        $this->assertFalse($this->elo->isValid('7066993918602422'));
        $this->assertFalse($this->elo->isValid('6066993918602422'));
        $this->assertFalse($this->elo->isValid('5042193918602422'));
        $this->assertFalse($this->elo->isValid('5002193918602422'));
    }

    #[test]
    public function testInvalidEloPANNotInIIN(): void
    {
        $this->assertFalse($this->elo->isValid('5098838050348643'));
        $this->assertFalse($this->elo->isValid('5198838050348643'));
    }

    #[test]
    public function testGenerateEloPANUsesConfiguredLengthAndPrefix(): void
    {
        $pan = $this->elo->generate();
        $iinList = $this->elo->getIINList();

        $this->assertMatchesRegularExpression('/^\d{16}$/', $pan);
        $this->assertContains((int)substr($pan, 0, 6), $iinList);
    }

    #[test]
    public function testGenerateEloPANNearestLengthStillUses16Digits(): void
    {
        $pan = $this->elo->generate(19);

        $this->assertMatchesRegularExpression('/^\d{16}$/', $pan);
    }

    #[test]
    public function testGenerateEloCVV(): void
    {
        $cvv = $this->elo->generateCVV();

        $this->assertMatchesRegularExpression('/^\d{3}$/', $cvv);
    }
}
