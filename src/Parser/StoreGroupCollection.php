<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\ParseContract;

class StoreGroupCollection extends Collection
{
    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $footer;

    /**
     * @param string $header
     *
     * @return \self
     */
    public function setHeader(string $header) : self
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @param string $footer
     *
     * @return \self
     */
    public function setFooter(string $footer) : self
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @param string $stream
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    protected function parseCount(string $stream) : array
    {
        if (! (mb_substr($stream, 0, 2) == 'FE')) {
            throw new InvalidArgumentException('Format error.');
        }

        return [
            'total' => intval(mb_substr($stream, 2, 7)),
            ParseContract::ALIAS_AUTHORIZE => intval(mb_substr($stream, 9, 7)),
            ParseContract::ALIAS_REFUND => intval(mb_substr($stream, 16, 7)),
        ];
    }

    /**
     * @return array
     */
    public function totalRows() : array
    {
        return static::parseCount($this->footer);
    }

    public function dump()
    {
        return $this->toArray();
    }

    public function toArray()
    {
        return $this->flatMap(function ($store) {
            return $store->toArray();
        })->all();
    }
}
