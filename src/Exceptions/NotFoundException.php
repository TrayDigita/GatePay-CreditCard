<?php
declare(strict_types=1);

namespace GatePay\CreditCard\Exceptions;

use RuntimeException;

class NotFoundException extends RuntimeException implements ExceptionInterface
{
}
