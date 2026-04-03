<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests;

use GatePay\CreditCard\Brands\Visa;
use GatePay\CreditCard\CreditCard;
use GatePay\CreditCard\Exceptions\NotFoundException;
use GatePay\CreditCard\Interfaces\CreditCardBrandInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function array_keys;
use function count;

class CreditCardTest extends TestCase
{
    private CreditCard $creditCardDefault;


    protected function setUp(): void
    {
        $this->creditCardDefault = new CreditCard();
    }

    #[test]
    public function testHasBrandReturnsTrueForExistingBrand(): void
    {
        $this->assertTrue($this->creditCardDefault->has('visa'));
        $this->assertTrue($this->creditCardDefault->has('Visa'));
        $this->assertTrue($this->creditCardDefault->has('VISA'));
        $this->assertTrue($this->creditCardDefault->has('mastercard'));
        $this->assertTrue($this->creditCardDefault->has('amex'));
    }

    #[test]
    public function testHasBrandReturnsGPNDisabledByDefault(): void
    {
        $this->assertTrue($this->creditCardDefault->has('visa'));
        $this->assertTrue($this->creditCardDefault->has('Visa'));
        $this->assertTrue($this->creditCardDefault->has('VISA'));
        $this->assertTrue($this->creditCardDefault->has('mastercard'));
        $this->assertTrue($this->creditCardDefault->has('amex'));
        $this->assertFalse($this->creditCardDefault->has('gpn'));
    }

    public function testKeyNormalizer(): void
    {
        $this->assertSame('visa', $this->creditCardDefault->normalizeBrandId('ViSa'));
        $this->assertSame('visa', $this->creditCardDefault->normalizeBrandId('ViSa'));
        $visa = $this->creditCardDefault->get('visa');
        $this->assertSame('visa', $this->creditCardDefault->normalizeBrandId($visa));
        $this->assertSame('visa', $this->creditCardDefault->normalizeBrandId($visa));
    }

    #[test]
    public function testHasBrandReturnsFalseForNonExistingBrand(): void
    {
        $this->assertFalse($this->creditCardDefault->has('nonexistentbrand'));
        $this->assertFalse($this->creditCardDefault->has('foobar'));
    }

    #[test]
    public function testGetBrandReturnsValidBrandInterface(): void
    {
        $visa = $this->creditCardDefault->get('visa');
        $this->assertInstanceOf(Visa::class, $visa);
        $this->assertSame('visa', $visa->getId());
    }

    #[test]
    public function testGetBrandIsCaseInsensitive(): void
    {
        $visa1 = $this->creditCardDefault->get('visa');
        $visa2 = $this->creditCardDefault->get('VISA');
        $visa3 = $this->creditCardDefault->get('Visa');

        $this->assertSame($visa1->getId(), $visa2->getId());
        $this->assertSame($visa1->getId(), $visa3->getId());
    }

    #[test]
    public function testGetBrandThrowsNotFoundExceptionForNonExistingBrand(): void
    {
        self::expectException(NotFoundException::class);
        self::expectExceptionMessage("Credit card brand for 'nonexistent' was not found.");

        $this->creditCardDefault->get('nonexistent');
    }

    #[test]
    public function testGetBrandsReturnsArrayOfBrands(): void
    {
        $brands = $this->creditCardDefault->getBrands();
        $this->assertIsArray($brands);
        $this->assertNotEmpty($brands);

        foreach ($brands as $brand) {
            $this->assertInstanceOf(CreditCardBrandInterface::class, $brand);
        }
    }

    #[test]
    public function testGetBrandsIncludesDefaultFactoryCards(): void
    {
        $brands = $this->creditCardDefault->getBrands();
        $brandIds = array_keys($brands);

        $expectedBrands = ['visa', 'mastercard', 'amex', 'maestro', 'jcb'];
        foreach ($expectedBrands as $expected) {
            $this->assertContains($expected, $brandIds);
        }
    }

    #[test]
    public function testAddCustomBrand(): void
    {
        $customBrand = $this->createMock(CreditCardBrandInterface::class);
        $customBrand->method('getId')->willReturn('customcard');
        $customBrand->method('getName')->willReturn('Custom Card');

        $result = $this->creditCardDefault->append($customBrand);
        $this->assertTrue($result);
        $this->assertTrue($this->creditCardDefault->has('customcard'));
        $resultFalse = $this->creditCardDefault->prepend($customBrand);
        $this->assertFalse($resultFalse);
        // remove custom brand
        $this->creditCardDefault->remove($customBrand);
        $this->assertFalse($this->creditCardDefault->has('customcard'));
        $result = $this->creditCardDefault->prepend($customBrand);
        $this->assertTrue($result);

        $creditCardDefault = new CreditCard(Visa::ID);
        $newVisa = new Visa();
        $resultFalseVisa = $creditCardDefault->prepend($newVisa);
        $this->assertTrue($resultFalseVisa);
        $this->assertSame($newVisa, $creditCardDefault->get(Visa::ID));
        $this->assertSame($newVisa, $creditCardDefault->get(Visa::class));
    }

    #[test]
    public function testAddDuplicateBrandReturnsFalse(): void
    {
        $customBrand = $this->createMock(CreditCardBrandInterface::class);
        $customBrand->method('getId')->willReturn('testcard');

        $this->creditCardDefault->append($customBrand);
        $this->assertTrue($this->creditCardDefault->has('testcard'));

        $customBrand2 = $this->createMock(CreditCardBrandInterface::class);
        $customBrand2->method('getId')->willReturn('testcard');

        $result = $this->creditCardDefault->append($customBrand2);
        $this->assertFalse($result);

        $creditCardDefault = new CreditCard(Visa::ID);
        $newVisa = new Visa();
        $resultFalseVisa = $creditCardDefault->append($newVisa);
        $this->assertTrue($resultFalseVisa);
        $this->assertSame($newVisa, $creditCardDefault->get(Visa::ID));
        $this->assertSame($newVisa, $creditCardDefault->get(Visa::class));
    }

    #[test]
    public function testAddExistingCoreBrandReturnsFalse(): void
    {
        $visaBrand = $this->createMock(CreditCardBrandInterface::class);
        $visaBrand->method('getId')->willReturn('visa');

        $result = $this->creditCardDefault->append($visaBrand);
        $this->assertFalse($result);
    }

    #[test]
    public function testReplaceCustomBrand(): void
    {
        $customBrand1 = $this->createMock(CreditCardBrandInterface::class);
        $customBrand1->method('getId')->willReturn('custom');
        $customBrand1->method('getName')->willReturn('Custom Card 1');

        $this->creditCardDefault->append($customBrand1);

        $customBrand2 = $this->createMock(CreditCardBrandInterface::class);
        $customBrand2->method('getId')->willReturn('custom');
        $customBrand2->method('getName')->willReturn('Custom Card 2');

        $previous = $this->creditCardDefault->replace($customBrand2);
        $this->assertNotNull($previous);
        $this->assertSame($customBrand1->getName(), $previous->getName());
        $oldVisa = $this->creditCardDefault->get(Visa::ID);
        $newVisa = new Visa();
        $resultFalseVisa = $this->creditCardDefault->replace($newVisa);
        $this->assertNotNull($resultFalseVisa);
        $this->assertSame($oldVisa, $resultFalseVisa);
        $this->assertNotSame($resultFalseVisa, $newVisa);
        $this->assertSame($newVisa, $this->creditCardDefault->get(Visa::ID));
        $this->assertSame($newVisa, $this->creditCardDefault->get(Visa::class));
    }

    #[test]
    public function testReplaceCoreBrand(): void
    {
        $visaBrand = $this->createMock(CreditCardBrandInterface::class);
        $visaBrand->method('getId')->willReturn('visa');

        $previous = $this->creditCardDefault->replace($visaBrand);
        $this->assertNotSame($previous, $visaBrand);
        $current = $this->creditCardDefault->get($visaBrand->getId());
        $this->assertSame($current, $visaBrand);
    }

    #[test]
    public function testRemoveCustomBrand(): void
    {
        $customBrand = $this->createMock(CreditCardBrandInterface::class);
        $customBrand->method('getId')->willReturn('temporary');

        $this->creditCardDefault->append($customBrand);
        $this->assertTrue($this->creditCardDefault->has('temporary'));

        $removed = $this->creditCardDefault->remove('temporary');
        $this->assertNotNull($removed);
        $this->assertFalse($this->creditCardDefault->has('temporary'));
    }

    #[test]
    public function testRemoveObjectAndReturnDefinition(): void
    {
        $default = new CreditCard();
        $previous = $default->remove('visa');
        $this->assertInstanceOf(Visa::class, $previous);
        $this->assertFalse($default->has('visa'));
    }

    #[test]
    public function testRemoveNonExistingBrandReturnsNull(): void
    {
        $previous = $this->creditCardDefault->remove('nonexistent');
        $this->assertNull($previous);
    }

    #[test]
    public function testGuessVisa(): void
    {
        $visaPan = '4532015112830366';
        $brand = $this->creditCardDefault->guess($visaPan);
        $this->assertSame('visa', $brand->getId());
    }

    #[test]
    public function testGuessMastercard(): void
    {
        $mastercardPan = '5329879707824603';
        $brand = $this->creditCardDefault->guess($mastercardPan);
        $this->assertSame('mastercard', $brand->getId());
    }

    #[test]
    public function testGuessAmericanExpress(): void
    {
        $amexPan = '376449047333005';
        $brand = $this->creditCardDefault->guess($amexPan);
        $this->assertSame('amex', $brand->getId());
    }

    #[test]
    public function testGuessThrowsNotFoundExceptionForInvalidPan(): void
    {
        self::expectException(NotFoundException::class);
        self::expectExceptionMessage("No matching credit card brand found for the provided PAN.");

        $this->creditCardDefault->guess('1234567890123456');
    }

    #[test]
    public function testMultipleInstancesAreIndependent(): void
    {
        $creditCard1 = new CreditCard();
        $creditCard2 = new CreditCard();

        $customBrand = $this->createMock(CreditCardBrandInterface::class);
        $customBrand->method('getId')->willReturn('onlyinfirst');

        $creditCard1->append($customBrand);

        $this->assertTrue($creditCard1->has('onlyinfirst'));
        $this->assertFalse($creditCard2->has('onlyinfirst'));
    }

    #[test]
    public function testBrandNormalizationHandlesWhitespace(): void
    {
        $this->assertTrue($this->creditCardDefault->has('  visa  '));
        $this->assertTrue($this->creditCardDefault->has("\tvisa\n"));
    }

    public function testObjectCount() : void
    {
        $default = new CreditCard();
        $listFactory = array_keys(CreditCard::FACTORY_CARDS);
        $factoryCount = count($listFactory);
        $this->assertSame($factoryCount, count($default));
        $this->assertSame($factoryCount, $default->count());
        $defaultEmpty = new CreditCard(...$listFactory);
        $this->assertSame(0, count($defaultEmpty));
        $this->assertSame(0, $defaultEmpty->count());
    }

    public function testMaskCard() : void
    {
        $visaPan = '4532015112830366';
        $masked = CreditCard::mask($visaPan);
        $this->assertSame('4532********0366', $masked);
        $veryShort = '1234';
        $maskedShort = CreditCard::mask($veryShort);
        $this->assertSame('1234', $maskedShort);
        $eightDigits = '12345678';
        $maskedEight = CreditCard::mask($eightDigits);
        $this->assertSame('******78', $maskedEight);
        $tenDigits = '1234567890';
        $maskedTen = CreditCard::mask($tenDigits);
        $this->assertSame('12******90', $maskedTen);
        $thirdTenDigits = '1234567890121';
        $maskedThirdTen = CreditCard::mask($thirdTenDigits);
        $this->assertSame('1234******121', $maskedThirdTen);
    }
}
