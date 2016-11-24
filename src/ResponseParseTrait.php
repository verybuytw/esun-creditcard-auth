<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser\StoreCollection;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser\StoreGroupCollection;

trait ResponseParseTrait
{
    protected $header;

    protected $footer;

    /**
     * @param array $rows
     *
     * @return Collection
     */
    protected function filterRows(array $rows) : Collection
    {
        $collection = StoreCollection::make($rows)->filter(function ($row) {
            return $row != '';
        });

        $this->header = $collection->shift();
        $this->footer = $collection->pop();

        return $collection->reverse()->values();
    }

    protected function throwInvalidArgumentException()
    {
        throw new InvalidArgumentException('Format error.');
    }

    protected function parseCount($stream) : array
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

    protected function parseBody(Collection $collection)
    {
        return $collection->pipe(function ($colletion) {
            return static::recursiveBody($colletion);
        })
            ->reverse()
            ->map(function ($collection) {
                return $collection->process();
            })
            ->keyBy(function ($collection) {
                return $collection->getCompanyId();
            })
            ->setHeader($this->header)
            ->setFooter($this->footer);
    }

    private function recursiveBody(Collection $collection)
    {
        $count = static::parseCount($collection->first());

        $splice = $collection->splice(0, $count['total'] + 2);

        if ($collection->isEmpty()) {
            return StoreGroupCollection::make([$splice]);
        }

        return static::recursiveBody($collection)->prepend($splice);
    }
}
