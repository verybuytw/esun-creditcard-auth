<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Generator;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class StoreGroupCollection extends Collection
{
    /**
     * @return StoreCollection|InvalidArgumentException
     */
    public function getMaster()
    {
        $master = $this->first(function ($collect) {
            return $collect->isMaster();
        });

        if (is_null($master)) {
            throw new InvalidArgumentException('Not found master StoreCollection in StoreGroupCollection.');
        }

        return $master;
    }

    public function push($value) : void
    {
        if (! ($value instanceof StoreCollection)) {
            throw new InvalidArgumentException('Not allowed.');
        }

        parent::push($value);
    }

    public function put($key, $value) : void
    {
        if (! ($value instanceof StoreCollection)) {
            throw new InvalidArgumentException('Not allowed.');
        }

        parent::put($key, $value);
    }

    public function totalRows() : int
    {
        return $this->sum(function ($collect) {
            return $collect->totalRows();
        });
    }

    public function toStream() : array
    {
        return $this->map(function ($collect) {
            return $collect->toStream();
        })->values()->all();
    }
}
