<?php
declare(strict_types=1);

namespace GatePay\CreditCard;

enum CardType: int
{
    /**
     * Represents a debit card, which is linked to a bank account and allows the cardholder
     * to spend money by drawing on funds they have deposited.
     */
    case DEBIT = 1;

    /**
     * Represents a credit card, which allows the cardholder to borrow funds from the card issuer
     * up to a certain limit in order to make purchases or withdraw cash.
     */
    case CREDIT = 2;
}
