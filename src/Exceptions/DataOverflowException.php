<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Exceptions;

use OverflowException;
use Throwable;

/**
 * Implemented when a data overflow occurs,
 * such as when a value exceeds the maximum allowed limit
 * or when an operation results in a value that is too large to be handled.
 */
class DataOverflowException extends OverflowException implements ExceptionInterface
{
    /**
     * DataOverflowException constructor.
     *
     * @param int $limit The maximum allowed limit that was exceeded.
     * @param string $message An optional custom error message. If not provided,
     *      a default message will be generated using the limit value.
     * @param int $code An optional error code. Default is 0.
     * @param Throwable|null $previous An optional previous exception for chaining. Default is null.
     */
    public function __construct(
        public readonly int $limit,
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
