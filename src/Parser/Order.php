<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser;

class Order
{
    const RESPONSE_SUCCESS = '00';

    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getOrderNumber() : string
    {
        return $this->options['order_number'];
    }

    /**
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return $this->options['return_code'] == self::RESPONSE_SUCCESS;
    }

    /**
     * @return string
     */
    public function type() : string
    {
        return $this->options['type'];
    }

    /**
     * @return float
     */
    public function amount() : float
    {
        return $this->options['amount'];
    }

    public function toArray()
    {
        return [
            'order_number' => $this->getOrderNumber(),
            'type' => $this->type(),
            'amount' => $this->amount(),
            'success' => $this->isSuccessful(),
        ];
    }
}
