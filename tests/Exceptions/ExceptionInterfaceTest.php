<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Exceptions;

use GatePay\CreditCard\Exceptions\DataOverflowException;
use GatePay\CreditCard\Exceptions\ExceptionInterface;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use GatePay\CreditCard\Exceptions\InvalidRangeException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;
use function is_subclass_of;

class ExceptionInterfaceTest extends TestCase
{
    #[test]
    public function testExceptionInterfaceExtendsThrowable(): void
    {
        $this->assertTrue(is_subclass_of(ExceptionInterface::class, Throwable::class));
    }

    #[test]
    public function testDataOverflowExceptionImplementsExceptionInterface(): void
    {
        $exception = new DataOverflowException(10);
        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testInvalidDataTypeExceptionImplementsExceptionInterface(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');
        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testInvalidRangeExceptionImplementsExceptionInterface(): void
    {
        $exception = new InvalidRangeException('Out of range');
        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testAllExceptionsCanBeThrown(): void
    {
        $exceptions = [
            new DataOverflowException(10),
            new InvalidDataTypeException('string', 'integer'),
            new InvalidRangeException('Out of range'),
        ];

        foreach ($exceptions as $exception) {
            $this->assertInstanceOf(Throwable::class, $exception);
        }
    }

    #[test]
    public function testExceptionInterfaceCanBeUsedForCatching(): void
    {
        $this->expectException(ExceptionInterface::class);

        throw new InvalidRangeException('Test error');
    }

    #[test]
    public function testMultipleExceptionTypesCaughtByInterface(): void
    {
        $exceptions = [
            new DataOverflowException(10),
            new InvalidDataTypeException('string', 'integer'),
            new InvalidRangeException('Out of range'),
        ];

        foreach ($exceptions as $exception) {
            try {
                throw $exception;
            } catch (Throwable $e) {
                $this->assertInstanceOf(ExceptionInterface::class, $e);
            }
        }
    }

    #[test]
    public function testExceptionChainCanUseExceptionInterface(): void
    {
        $root = new DataOverflowException(10, 'Root cause');
        $middle = new InvalidDataTypeException('string', 'integer', '', 0, $root);
        $top = new InvalidRangeException('Top error', 0, $middle);

        try {
            throw $top;
        } catch (Throwable $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e->getPrevious());
            $this->assertInstanceOf(DataOverflowException::class, $e->getPrevious()->getPrevious());
        }
    }
}
