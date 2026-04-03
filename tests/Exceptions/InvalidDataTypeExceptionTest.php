<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Exceptions;

use GatePay\CreditCard\Exceptions\ExceptionInterface;
use GatePay\CreditCard\Exceptions\InvalidDataTypeException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TypeError;

class InvalidDataTypeExceptionTest extends TestCase
{
    #[test]
    public function testConstructorStoresExpectedAndActualTypes(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        $this->assertSame('string', $exception->expectedType);
        $this->assertSame('integer', $exception->actualType);
    }

    #[test]
    public function testConstructorGeneratesDefaultMessage(): void
    {
        $exception = new InvalidDataTypeException('string', 'array');

        $this->assertStringContainsString('string', $exception->getMessage());
        $this->assertStringContainsString('array', $exception->getMessage());
        $this->assertStringContainsString('Invalid data type', $exception->getMessage());
    }

    #[test]
    public function testConstructorWithCustomMessage(): void
    {
        $customMessage = 'This is a custom message';
        $exception = new InvalidDataTypeException('string', 'integer', $customMessage);

        $this->assertSame($customMessage, $exception->getMessage());
    }

    #[test]
    public function testConstructorWithEmptyCustomMessage(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer', '');

        $this->assertStringContainsString('Invalid data type', $exception->getMessage());
    }

    #[test]
    public function testConstructorWithErrorCode(): void
    {
        $code = 999;
        $exception = new InvalidDataTypeException('string', 'integer', '', $code);

        $this->assertSame($code, $exception->getCode());
    }

    #[test]
    public function testConstructorWithPreviousException(): void
    {
        $previous = new RuntimeException('Previous error');
        $exception = new InvalidDataTypeException('string', 'integer', '', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testConstructorWithAllParameters(): void
    {
        $previous = new RuntimeException('Caused by');
        $exception = new InvalidDataTypeException(
            'numeric-string',
            'boolean',
            'Type mismatch detected',
            42,
            $previous
        );

        $this->assertSame('numeric-string', $exception->expectedType);
        $this->assertSame('boolean', $exception->actualType);
        $this->assertSame('Type mismatch detected', $exception->getMessage());
        $this->assertSame(42, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[test]
    public function testImplementsExceptionInterface(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }

    #[test]
    public function testExtendsInvalidArgumentException(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
    }

    #[test]
    public function testDefaultMessageIncludesTypeNames(): void
    {
        $exception = new InvalidDataTypeException('array', 'object');
        $message = $exception->getMessage();

        $this->assertStringContainsString('array', $message);
        $this->assertStringContainsString('object', $message);
    }

    #[test]
    public function testCanBeUsedWithVariousTypeNames(): void
    {
        $types = ['string', 'integer', 'float', 'array', 'object', 'resource', 'null', 'boolean'];

        foreach ($types as $expected) {
            foreach ($types as $actual) {
                if ($expected !== $actual) {
                    $exception = new InvalidDataTypeException($expected, $actual);
                    $this->assertSame($expected, $exception->expectedType);
                    $this->assertSame($actual, $exception->actualType);
                }
            }
        }
    }

    #[test]
    public function testCanBeCaughtAsInvalidArgumentException(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        try {
            throw $exception;
        } catch (InvalidArgumentException $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
        }
    }

    #[test]
    public function testCanBeCaughtAsExceptionInterface(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        try {
            throw $exception;
        } catch (ExceptionInterface $e) {
            $this->assertInstanceOf(InvalidDataTypeException::class, $e);
        }
    }

    #[test]
    public function testCustomMessageOverridesDefault(): void
    {
        $customMessage = 'Expected numeric value';
        $exception = new InvalidDataTypeException('numeric', 'string', $customMessage);

        $this->assertSame($customMessage, $exception->getMessage());
        $this->assertStringNotContainsString('Invalid data type', $exception->getMessage());
    }

    #[test]
    public function testTypesPropertiesArePublic(): void
    {
        $exception = new InvalidDataTypeException('string', 'integer');

        $this->assertTrue(isset($exception->expectedType));
        $this->assertTrue(isset($exception->actualType));
        $this->assertSame('string', $exception->expectedType);
        $this->assertSame('integer', $exception->actualType);
    }

    #[test]
    public function testExceptionChaining(): void
    {
        $previous = new TypeError('Type error occurred');
        $exception = new InvalidDataTypeException('int', 'string', '', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame('Type error occurred', $exception->getPrevious()->getMessage());
    }

    #[test]
    public function testMultipleTypeDescriptions(): void
    {
        $exception = new InvalidDataTypeException('numeric-string', 'numeric-float');

        $this->assertSame('numeric-string', $exception->expectedType);
        $this->assertSame('numeric-float', $exception->actualType);
        $this->assertStringContainsString('numeric-string', $exception->getMessage());
        $this->assertStringContainsString('numeric-float', $exception->getMessage());
    }
}
