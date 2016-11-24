<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\ParseContract;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser\Order;

trait StoreCollectionParserTrait
{
    /**
     * @param string $stream
     * @return string
     */
    protected function parseCompanyId(string $stream) : string
    {
        if (! (mb_substr($stream, 0, 2) == 'FH')) {
            static::throwInvalidArgumentException();
        }

        return trim(mb_substr($stream, 2, 15));
    }

    /**
     * @param string $stream
     * @return Order
     */
    protected function parseOrder(string $stream) : Order
    {
        if (! (mb_substr($stream, 0, 2) == 'FD')) {
            static::throwInvalidArgumentException();
        }

        return new Order([
            'order_number' => trim(mb_substr($stream, 2, 20)),
            'type' => static::parseType(mb_substr($stream, 22, 2)),
            'amount' => floatval(mb_substr($stream, 24, 12)) / 100,
            'return_code' => mb_substr($stream, 36, 2),
            'description' => [
                'en' => mb_substr($stream, 38, 25),
                'zh_TW' => mb_substr($stream, 63, 40),
            ],
            'comment' => [
                mb_substr($stream, 103, 20),
                mb_substr($stream, 123, 25),
            ],
        ]);
    }

    /**
     * @param string $type
     * @return string
     */
    protected function parseType(string $type) : string
    {
        $types = [
            ParseContract::TYPE_AUTHORIZE => ParseContract::ALIAS_AUTHORIZE,
            ParseContract::TYPE_REFUND => ParseContract::ALIAS_REFUND,
        ];

        if (! array_key_exists($type, $types)) {
            static::throwInvalidArgumentException();
        }

        return $types[$type];
    }
}
