<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

trait StoreCollectionTrait
{
    /**
     * @var string
     */
    protected $companyId;

    /**
     * @param string $companyId
     *
     * @return self
     */
    public function setCompanyId(string $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return $this->companyId;
    }
}
