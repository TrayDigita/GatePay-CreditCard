<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Exceptions;

use RangeException;

/**
 * Implemented when a range of values is invalid,
 * such as an invalid range of indices or an invalid range of values in a data structure.
 */
class InvalidRangeException extends RangeException implements ExceptionInterface
{
}
