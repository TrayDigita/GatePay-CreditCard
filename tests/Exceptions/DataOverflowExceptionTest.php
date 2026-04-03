<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Exceptions;

use GatePay\CreditCard\Exceptions\DataOverflowException;
use GatePay\CreditCard\Exceptions\ExceptionInterface;
use Exception;
use OverflowException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\testCase;
use ReflectionObject;
use RuntimeException;
use const PHP_INT_MAX;

class DataOverflowExceptionTest extends testCase
{
    #[test]
    public function testConstructorStoresLimitAsReadonlyProperty(): void
    {
        $limit = 10;
        $exception = new DataOverflowException($limit);

        $this->assertSame($limit, $exception->limit);
    }

    #[test]
    public function testConstructorWithDifferentLimitValues(): void
    {
        $limits = [0, 1, 100, 999999, PHP_INT_MAX];

        foreach ($limits as $limit) {
            $exception = new DataOverflowException($limit);
            $this->assertSame($limit, $exception->limit);
        }
    }

    #[test]
    public function testConstructorWithCustomMessage(): void
    {
        $limit = 5;
        $message = 'Custom error message';
        $exception = new DataOverflowException($limit, $message);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($limit, $exception->limit);
    }

    #[test]
    public function testConstructorWithEmptyMessage(): void
    {
        $limit = 10;
        $exception = new DataOverflowException($limit, '');

        $this->assertEmpty($exception->getMessage());
    }

    #[test]
    public function testConstructorWithErrorCode(): void
    {
        $limit = 10;
        $code = 42;
        $exception = new DataOverflowException($limit, 'message', $code);

        $this->assertSame($code, $exception->getCode());
    }

    #[test]
    public function testConstructorWithPreviousException(): void
    {
        $limit = 10;
        $previous = new RuntimeException('Previous error');
        $exception = new DataOverflowException($limit, 'message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testConstructorWithAllParameters(): void
    {
        $limit = 100;
        $message = 'Complete error message';
        $code = 123;
        $previous = new Exception('Previous exception');

        $exception = new DataOverflowException($limit, $message, $code, $previous);

        $this->assertSame($limit, $exception->limit);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testImplementsExceptionInterface(): void
    {
        $exception = new DataOverflowException(10);

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testExtendsOverflowException(): void
    {
        $exception = new DataOverflowException(10);

        $this->assertInstanceOf(OverflowException::class, $exception);
    }

    #[test]
    public function testLimitPropertyIsReadonly(): void
    {
        $exception = new DataOverflowException(10);

        $this->assertInstanceOf(OverflowException::class, $exception);
        $this->assertSame(10, $exception->limit);
        $ref = new ReflectionObject($exception);
        $this->assertTrue($ref->getProperty('limit')->isReadOnly());
    }

    #[test]
    public function testCanBeCaughtAsOverflowException(): void
    {
        $exception = new DataOverflowException(5);

        try {
            throw $exception;
        } catch (OverflowException $e) {
            $this->assertSame(5, $e->limit);
            $this->assertInstanceOf(DataOverflowException::class, $e);
        }
    }

    #[test]
    public function testCanBeCaughtAsExceptionInterface(): void
    {
        $exception = new DataOverflowException(15);

        try {
            throw $exception;
        } catch (ExceptionInterface $e) {
            $this->assertInstanceOf(DataOverflowException::class, $e);
        }
    }

    #[test]
    public function testMessageCanBeGeneratedFromLimitValue(): void
    {
        $limit = 42;
        $message = 'The limit of ' . $limit . ' was exceeded';
        $exception = new DataOverflowException($limit, $message);

        $this->assertStringContainsString((string)$limit, $exception->getMessage());
    }

    #[test]
    public function testNegativeZeroAndPositiveLimits(): void
    {
        $negativeException = new DataOverflowException(-1);
        $zeroException = new DataOverflowException(0);
        $positiveException = new DataOverflowException(1);

        $this->assertSame(-1, $negativeException->limit);
        $this->assertSame(0, $zeroException->limit);
        $this->assertSame(1, $positiveException->limit);
    }

    #[test]
    public function testExceptionChaining(): void
    {
        $previous = new \InvalidArgumentException('Invalid argument');
        $exception = new DataOverflowException(10, 'Overflow occurred', 0, $previous);

        $trace = $exception->getPrevious();
        $this->assertSame($previous, $trace);
        $this->assertSame('Invalid argument', $trace->getMessage());
    }

    #[test]
    public function testMultipleExceptionChainingLevels(): void
    {
        $root = new Exception('Root cause');
        $middle = new RuntimeException('Middle error', 0, $root);
        $top = new DataOverflowException(100, 'Top overflow', 0, $middle);

        $this->assertSame($middle, $top->getPrevious());
        $this->assertSame($root, $middle->getPrevious());
    }
}
