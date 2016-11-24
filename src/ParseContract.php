<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

interface ParseContract
{
    const TYPE_AUTHORIZE = '05';
    const TYPE_REFUND = '06';
    const ALIAS_AUTHORIZE = 'authorize';
    const ALIAS_REFUND = 'refund';
    const ENTER_CRLF = "\r\n";
    const ENTER_LF = "\n";
}
