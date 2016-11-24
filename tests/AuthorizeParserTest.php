<?php

namespace Tests\Payment\EsunBank\CreditCard\Order\Authorize;

use Tests\Payment\EsunBank\CreditCard\Order\Authorize\AbstractTestCase;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\AuthorizeParser;
use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\Parser\StoreGroupCollection;

class AuthorizeParserTest extends AbstractTestCase
{
    protected $parser;

    public function setUp()
    {
        $filePath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'D617$2016112001O.TXT';

        $this->parser = new AuthorizeParser($filePath);
    }

    public function testAuthorizeParser()
    {
        $this->assertInstanceOf(StoreGroupCollection::class, $this->parser->getParsedCollection());

        $collection = $this->parser->getParsedCollection();

        dump($collection->dump());
    }
}
