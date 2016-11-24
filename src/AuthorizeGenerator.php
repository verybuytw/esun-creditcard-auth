<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

use Carbon\Carbon;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\Handler;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\FileHandlerTrait as FileHandler;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Generator\StoreCollection;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Generator\StoreGroupCollection;

class AuthorizeGenerator
{
    use FileHandler;

    const FORMAT_GENERATE_FILE = '%s$%s%02dI.txt';
    const FORMAT_HEADER = 'FS%- 15s% 4s%04s';
    const FORMAT_FOOTER = 'FE%07s';

    /**
     * @var string
     */
    protected $vendorId;

    /**
     * @var StoreGroupCollection
     */
    protected $stores;

    /**
     * @param string $vendorId
     * @param array  $options
     * @param string $folderPath
     */
    public function __construct(string $vendorId, array $options = [], string $folderPath = 'storages')
    {
        $fileHandler = new Filesystem(new Local($folderPath));

        $this->setVendorId($vendorId)
            ->setFileHandler($fileHandler);

        $this->stores = StoreGroupCollection::make($options)->map(function ($collect, $store) {
            if (! array_key_exists(ParseContract::ALIAS_AUTHORIZE, $collect)) {
                $collect[ParseContract::ALIAS_AUTHORIZE] = [];
            }

            if (! array_key_exists(ParseContract::ALIAS_REFUND, $collect)) {
                $collect[ParseContract::ALIAS_REFUND] = [];
            }

            return StoreCollection::make()
                ->setCompanyId($store)
                ->setMaster(array_key_exists('master', $collect) and $collect['master'] === true)
                ->authorize($collect[ParseContract::ALIAS_AUTHORIZE])
                ->refund($collect[ParseContract::ALIAS_REFUND]);
        });
    }

    /**
     * @param string $vendorId
     *
     * @return self
     */
    public function setVendorId(string $vendorId) : self
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    /**
     * @return string
     */
    protected function getVendorId() : string
    {
        return $this->vendorId;
    }

    /**
     * @return StoreGroupCollection
     */
    protected function getStoreGroupCollection() : StoreGroupCollection
    {
        return $this->stores;
    }

    /**
     * @param int $num
     *
     * @return Handler
     */
    public function generateFile(int $num = 1)
    {
        $contents = static::generateStream();

        $path = static::genFileName($num);

        $handler = $this->getFileHandler();

        if ($handler->has($path)) {
            $handler->update($path, $contents);
        } else {
            $handler->write($path, $contents);
        }

        return $handler->get($path);
    }

    /**
     * @param int $num
     *
     * @return string
     */
    protected function genFileName(int $num)
    {
        return sprintf(
            self::FORMAT_GENERATE_FILE,
            $this->getVendorId(),
            Carbon::now()->format('Ymd'),
            $num
        );
    }

    /**
     * @return string
     */
    public function generateStream()
    {
        $records = array_prepend($this->stores->toStream(), static::genHeader());

        array_push($records, static::genFooter());

        $contents = implode(ParseContract::ENTER_CRLF, $records);

        return mb_convert_encoding($contents, 'BIG5', 'UTF-8');
    }

    /**
     * @return string
     */
    protected function genHeader() : string
    {
        return sprintf(
            self::FORMAT_HEADER,
            $this->getStoreGroupCollection()->getMaster()->getCompanyId(),
            null,
            $this->getStoreGroupCollection()->count()
        );
    }

    /**
     * @return string
     */
    protected function genFooter() : string
    {
        return sprintf(
            self::FORMAT_FOOTER,
            $this->getStoreGroupCollection()->totalRows()
        );
    }
}
