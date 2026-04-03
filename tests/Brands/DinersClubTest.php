<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Brands\DinersClub;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DinersClubTest extends TestCase
{
    private DinersClub $dinersClub;

    protected function setUp(): void
    {
        $this->dinersClub = new DinersClub();
    }

    #[test]
    public function testDinersClubImplementsCreditCardBrandInterface(): void
    {
        $this->assertInstanceOf(CreditCardBrandInterface::class, $this->dinersClub);
    }

    #[test]
    public function testDinersClubIdIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dinersClub->getId());
    }

    #[test]
    public function testDinersClubNameIsNotEmpty(): void
    {
        $this->assertNotEmpty($this->dinersClub->getName());
    }

    #[test]
    public function testDinersClubIINListIsValid(): void
    {
        $iinList = $this->dinersClub->getIINList();
        $this->assertIsArray($iinList);
        $this->assertNotEmpty($iinList);
    }

    #[test]
    public function testDinersClubPANLengthsAreValid(): void
    {
        $panLengths = $this->dinersClub->getPANLengths();
        $this->assertIsArray($panLengths);
        $this->assertNotEmpty($panLengths);
    }

    #[test]
    public function testDinersClubCVVLengthIsValid(): void
    {
        $cvvLengths = $this->dinersClub->getCVVLengths();
        $this->assertIsArray($cvvLengths);
        $this->assertNotEmpty($cvvLengths);
    }

    #[test]
    public function testDinersClubHasInvalidLength()
    {
        $invalidPan = '1234567890123';
        $this->assertFalse($this->dinersClub->isValid($invalidPan));
    }

    #[test]
    public function testDinersClubGeneratePAN(): void
    {
        $pan = $this->dinersClub->generate();
        $this->assertTrue($this->dinersClub->isValid($pan));
        $this->assertIsNumeric($pan);
    }

    #[test]
    public function testDinersClubGenerateCVV(): void
    {
        $cvv = $this->dinersClub->generateCVV();
        $this->assertIsNumeric($cvv);
    }
}
