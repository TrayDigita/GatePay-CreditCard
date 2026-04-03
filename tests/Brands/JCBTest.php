<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\JCB;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JCBTest extends TestCase
{
    private JCB $jcb;

    protected function setUp(): void
    {
        $this->jcb = new JCB();
    }

    #[test]
    public function testJCBImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->jcb);
    }

    #[test]
    public function testJCBIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->jcb->getId());
    }

    #[test]
    public function testJCBNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->jcb->getName());
    }

    #[test]
    public function testJCBIINListIsValid(): void
    {
        $iinList = $this->jcb->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testJCBPANLengthsAreValid(): void
    {
        $panLengths = $this->jcb->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testJCBCPanInvalidLength(): void
    {
        $this->assertFalse($this->jcb->isValid('123456789012')); // Too short
        $this->assertFalse($this->jcb->isValid('12345678901234567890')); // Too long
    }

    #[test]
    public function testJCBCPanIsValid(): void
    {
        $validPan = '3536690211037485578'; // Valid JCB card number
        $this->assertTrue($this->jcb->isValid($validPan));
    }

    #[test]
    public function testJCBCPanIsValid62212x(): void
    {
        $invalidLuhnPan = '6221290211037485579'; // Invalid Luhn check digit
        $this->assertFalse($this->jcb->isValid($invalidLuhnPan));
    }

    #[test]
    public function testJCBCPanInvalidLuhn(): void
    {
        $invalidLuhnPan = '3536690211037485579'; // Invalid Luhn check digit
        $this->assertFalse($this->jcb->isValid($invalidLuhnPan));
    }

    #[test]
    public function testJCBCVVLengthIsValid(): void
    {
        $cvvLengths = $this->jcb->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testJCBGeneratePAN(): void
    {
        $pan = $this->jcb->generate();
        $this->assertTrue($this->jcb->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testJCBGenerateCVV(): void
    {
        $cvv = $this->jcb->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
