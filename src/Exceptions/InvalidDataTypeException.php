<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Exceptions;

use InvalidArgumentException;
use Throwable;
use function sprintf;

/**
 * Implemented when an invalid data type is encountered,
 * such as when a function or method receives an argument of an unexpected type.
 */
class InvalidDataTypeException extends InvalidArgumentException implements ExceptionInterface
{
    /**
     * InvalidDataTypeException constructor.
     *
     * @param string $expectedType The expected data type (e.g., "string", "integer").
     * @param string $actualType The actual data type that was encountered.
     * @param string $message An optional custom error message. If not provided,
     *      a default message will be generated using the expected and actual types.
     * @param int $code An optional error code. Default is 0.
     * @param Throwable|null $previous An optional previous exception for chaining. Default is null.
     */
    public function __construct(
        public string $expectedType,
        public string $actualType,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $message = $message ?: sprintf(
            'Invalid data type: expected "%s", got "%s".',
            $expectedType,
            $actualType
        );
        parent::__construct($message, $code, $previous);
    }
}
