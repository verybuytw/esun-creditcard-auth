<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser;

use Illuminate\Support\Collection;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\StoreCollectionTrait as StoreCollectionCommon;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\StoreCollectionParserTrait as ParserExtension;

class StoreCollection extends Collection
{
    use StoreCollectionCommon, ParserExtension;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $footer;

    /**
     * @var array
     */
    protected $original;

    /**
     * @param string $header
     *
     * @return \self
     */
    public function setHeader(string $header) : self
    {
        $this->header = $header;

        $this->setCompanyId(static::parseCompanyId($header));

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
     * @param array $original
     *
     * @return self
     */
    public function saveOriginal(array $original) : self
    {
        $this->original = $original;

        return $this;
    }

    /**
     * @param string $stream
     *
     * @return array
     */
    protected function parseCount(string $stream) : array
    {
        if (! (mb_substr($stream, 0, 2) == 'FT')) {
            static::throwInvalidArgumentException();
        }

        return [
            'total' => intval(mb_substr($stream, 2, 6)),
            ParseContract::ALIAS_AUTHORIZE => intval(mb_substr($stream, 8, 6)),
            ParseContract::ALIAS_REFUND => intval(mb_substr($stream, 14, 6)),
        ];
    }

    public function totalRows()
    {
        return static::parseCount($this->footer);
    }

    /**
     * @return self
     */
    public function process()
    {
        $origials = $this->all();
        $footer = $this->shift();
        $header = $this->pop();

        $collection = $this->map(function ($row) {
            return static::parseOrder($row);
        })->keyBy(function ($order) {
            return $order->getOrderNumber();
        });

        return $collection->saveOriginal($origials)
            ->setHeader($header)
            ->setFooter($footer);
    }

    public function toArray()
    {
        return $this->map(function ($order) {
            return array_merge([
                'company_id' => $this->getCompanyId(),
            ], $order->toArray());
        })->values()->all();
    }
}
