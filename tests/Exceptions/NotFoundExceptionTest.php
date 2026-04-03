<?php
declare(strict_types=1);

namespace GatePay\CreditCardTests\Exceptions;

use GatePay\CreditCard\Exceptions\ExceptionInterface;
use GatePay\CreditCard\Exceptions\NotFoundException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NotFoundExceptionTest extends TestCase
{
    #[Test]
    public function testItCreatesExceptionWithDefaultValues(): void
    {
        $exception = new NotFoundException();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    #[Test]
    public function testItStoresCustomMessageCodeAndPreviousException(): void
    {
        $previous = new RuntimeException('previous error');

        $exception = new NotFoundException('Card not found', 404, $previous);

        $this->assertSame('Card not found', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function testItIsCaughtAsRuntimeException(): void
    {
        try {
            throw new NotFoundException('missing');
        } catch (RuntimeException $exception) {
            $this->assertInstanceOf(NotFoundException::class, $exception);
            $this->assertSame('missing', $exception->getMessage());
        }
    }

    #[Test]
    public function testItImplementsExceptionInterface(): void
    {
        $exception = new NotFoundException('missing');

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
    }
}
