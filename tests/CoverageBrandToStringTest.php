<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests;

use GatePay\CreditCard\CreditCard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CoverageBrandToStringTest extends TestCase
{
    #[test]
    public function testToString() : void
    {
        $creditCard = new CreditCard();
        foreach ($creditCard->getBrands() as $brand) {
            $this->assertSame($brand->getName(), (string)$brand);
            $this->assertSame($brand->getName(), $brand->__toString());
        }
    }
}
