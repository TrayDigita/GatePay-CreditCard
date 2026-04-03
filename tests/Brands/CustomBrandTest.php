<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Brands;

use GatePay\CreditCard\Abstracts\AbstractCreditCardBrand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CustomBrandTest extends TestCase
{
    protected AbstractCreditCardBrand $brand;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->brand = new class extends AbstractCreditCardBrand {
            public function __construct()
            {
                $this->id = 'custom';
                $this->name = 'Custom Brand';
                $this->iinList = ['1234'];
                $this->panLengths = [16];
            }
        };
    }

    #[test]
    public function testInvalidPanLengthBase()
    {
        $invalidLength = '1234567';
        $this->assertFalse($this->brand->isValid($invalidLength));
    }

    #[test]
    public function testInvalidPanLength()
    {
        $invalidLength = '12345678901234567'; // 17 digits, while the brand expects 16
        $this->assertFalse($this->brand->isValid($invalidLength));
    }

    #[test]
    public function testValidPan()
    {
        $validPan = '1234567475557132';
        $this->assertTrue($this->brand->isValid($validPan));
    }

    #[test]
    public function testInvalidPan()
    {
        $invalidpan = '1122171501098594';
        $this->assertFalse($this->brand->isValid($invalidpan));
    }

    #[test]
    public function testInvalidPanLuhn()
    {
        $invalidPan = '1234567475557131';
        $this->assertFalse($this->brand->isValid($invalidPan));
    }

    #[test]
    public function testGeneratePanWithNearest()
    {
        $generated = $this->brand->generate(15);
        $this->assertTrue($this->brand->isValid($generated));
    }
}
