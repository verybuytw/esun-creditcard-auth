<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

use League\Flysystem\File;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\FileHandlerTrait as FileHandler;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser\StoreGroupCollection;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\ResponseParseTrait as ResponseParse;

class AuthorizeParser
{
    use FileHandler, ResponseParse;

    /**
     * @var File
     */
    protected $resource;

    /**
     * @param string $resource
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
    }

    /**
     * @return string
     */
    protected function getContents() : string
    {
        return $this->getFileHandler()->get($this->resource)->read();
    }

    /**
     * @return StoreGroupCollection
     */
    public function getParsedCollection() : StoreGroupCollection
    {
        $exploed = explode(ParseContract::ENTER_CRLF, self::getContents());

        $collection = static::filterRows($exploed);

        return static::parseBody($collection);
    }
}
