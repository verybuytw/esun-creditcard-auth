<?php

namespace VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

trait FileHandlerTrait
{
    /**
     * @var Filesystem
     */
    protected $fileHandler;

    /**
     * @return Filesystem
     */
    protected function getFileHandler() : Filesystem
    {
        if (is_null($this->fileHandler)) {
            $this->fileHandler = new Filesystem(new Local('/'));
        }

        return $this->fileHandler;
    }

    /**
     * @param Filesystem $fileHandler
     *
     * @return self
     */
    protected function setFileHandler(Filesystem $fileHandler) : self
    {
        $this->fileHandler = $fileHandler;

        return $this;
    }
}
