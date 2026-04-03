<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Exceptions;

use GatePay\CreditCard\Exceptions\ExceptionInterface;
use GatePay\CreditCard\Exceptions\InvalidRangeException;
use OutOfRangeException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RangeException;
use RuntimeException;
use function strtolower;

class InvalidRangeExceptionTest extends TestCase
{
    #[test]
    public function testImplementsExceptionInterface(): void
    {
        $exception = new InvalidRangeException();

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testExtendsRangeException(): void
    {
        $exception = new InvalidRangeException();

        $this->assertInstanceOf(RangeException::class, $exception);
    }

    #[test]
    public function testConstructorWithDefaultParameters(): void
    {
        $exception = new InvalidRangeException();

        $this->assertEmpty($exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    #[test]
    public function testConstructorWithCustomMessage(): void
    {
        $message = 'Value is out of acceptable range';
        $exception = new InvalidRangeException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    #[test]
    public function testConstructorWithErrorCode(): void
    {
        $code = 123;
        $exception = new InvalidRangeException('Out of range', $code);

        $this->assertSame($code, $exception->getCode());
    }

    #[test]
    public function testConstructorWithPreviousException(): void
    {
        $previous = new RuntimeException('Cause');
        $exception = new InvalidRangeException('Message', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testConstructorWithAllParameters(): void
    {
        $message = 'Invalid range detected';
        $code = 42;
        $previous = new \LogicException('Previous error');

        $exception = new InvalidRangeException($message, $code, $previous);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testCanBeCaughtAsRangeException(): void
    {
        $exception = new InvalidRangeException('Out of range');

        try {
            throw $exception;
        } catch (RangeException $e) {
            $this->assertInstanceOf(InvalidRangeException::class, $e);
            $this->assertSame('Out of range', $e->getMessage());
        }
    }

    #[test]
    public function testCanBeCaughtAsExceptionInterface(): void
    {
        $exception = new InvalidRangeException('Invalid range');

        try {
            throw $exception;
        } catch (ExceptionInterface $e) {
            $this->assertInstanceOf(InvalidRangeException::class, $e);
        }
    }

    #[test]
    public function testExceptionChaining(): void
    {
        $previous = new OutOfRangeException('Original cause');
        $exception = new InvalidRangeException('Wrapped error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame('Original cause', $exception->getPrevious()->getMessage());
    }

    #[test]
    public function testMultipleExceptionChainingLevels(): void
    {
        $root = new \Exception('Root cause');
        $middle = new RuntimeException('Middle level', 0, $root);
        $top = new InvalidRangeException('Top level', 0, $middle);

        $this->assertSame($middle, $top->getPrevious());
        $this->assertSame($root, $middle->getPrevious());
    }

    #[test]
    public function testMessageCanContainRangeDetails(): void
    {
        $message = 'Expected range 1-100, got 150';
        $exception = new InvalidRangeException($message);

        $this->assertStringContainsString('range', strtolower($exception->getMessage()));
    }

    #[test]
    public function testEmptyMessageWithCode(): void
    {
        $exception = new InvalidRangeException('', 999);

        $this->assertEmpty($exception->getMessage());
        $this->assertSame(999, $exception->getCode());
    }

    #[test]
    public function testMultipleInstancesAreIndependent(): void
    {
        $exception1 = new InvalidRangeException('Error 1', 1);
        $exception2 = new InvalidRangeException('Error 2', 2);

        $this->assertSame('Error 1', $exception1->getMessage());
        $this->assertSame('Error 2', $exception2->getMessage());
        $this->assertSame(1, $exception1->getCode());
        $this->assertSame(2, $exception2->getCode());
    }
}
