<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Generator;

use Illuminate\Support\Collection;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\ParseContract;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\StoreCollectionTrait as StoreCollectionExtension;

class StoreCollection extends Collection
{
    use StoreCollectionExtension;

    const FORMAT_HEADER = 'FH%- 15s% 4s%sN% 7s';
    const FORMAT_FOOTER = 'FT%06s';
    const FORMAT_BODY = 'FD%- 20s%s%010s00% 2s%- 25s%s%- 20s%- 25s';

    /**
     * @var bool
     */
    protected $master;

    /**
     * @param bool $isMaster
     *
     * @return self
     */
    public function setMaster(bool $isMaster): self
    {
        $this->master = $isMaster;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMaster(): bool
    {
        return $this->master;
    }

    /**
     * @param string $category
     * @param array  $orders
     *
     * @return self
     */
    protected function push2Category(string $category, array $orders): self
    {
        if (! $this->has($category)) {
            $this->put($category, Collection::make([]));
        }

        foreach ($orders as $order) {
            $this->get($category)[] = $order;
        }

        return $this;
    }

    /**
     * @param array $orders
     *
     * @return self
     */
    public function authorize(array $orders): self
    {
        return $this->push2Category(__FUNCTION__, $orders);
    }

    /**
     * @param array $orders
     *
     * @return self
     */
    public function refund(array $orders): self
    {
        return $this->push2Category(__FUNCTION__, $orders);
    }

    /**
     * @return int
     */
    public function totalRows(): int
    {
        return $this->get(ParseContract::ALIAS_REFUND)->count() + $this->get(ParseContract::ALIAS_AUTHORIZE)->count();
    }

    /**
     * @return string
     */
    public function toStream(): string
    {
        $spaces = dbc2Sbc(str_repeat(' ', 20));

        $authorize = $this->get(ParseContract::ALIAS_AUTHORIZE)->map(function ($order) use ($spaces) {
            return sprintf(
                self::FORMAT_BODY,
                $order['order_number'],
                ParseContract::TYPE_AUTHORIZE,
                $order['amount'],
                null,
                null,
                $spaces,
                null,
                null
            );
        })->all();

        $refund = $this->get(ParseContract::ALIAS_REFUND)->map(function ($order) use ($spaces) {
            return sprintf(
                self::FORMAT_BODY,
                $order['order_number'],
                ParseContract::TYPE_REFUND,
                $order['amount'],
                null,
                null,
                $spaces,
                null,
                null
            );
        })->all();

        $records = array_merge($authorize, $refund);

        $records = array_prepend($records, static::genHeader());

        array_push($records, static::genFooter());

        return implode(ParseContract::ENTER_CRLF, $records);
    }

    /**
     * @return string
     */
    protected function genHeader(): string
    {
        return sprintf(
            self::FORMAT_HEADER,
            $this->getCompanyId(),
            null,
            null,
            null
        );
    }

    /**
     * @return string
     */
    protected function genFooter(): string
    {
        return sprintf(
            self::FORMAT_FOOTER,
            $this->totalRows()
        );
    }
}
