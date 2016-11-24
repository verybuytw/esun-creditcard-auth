<?php

namespace Tests\Payment\EsunBank\CreditCard\Order\Authorize;

use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\AuthorizeGenerator;

class AuthorizeGeneratorTest extends AbstractTestCase
{
    protected $generator;

    public function setUp()
    {
        $this->generator = new AuthorizeGenerator('D617', [
            '8089016171' => [
                'master' => true,
                'authorize' => [
                    [
                        'order_number' => 'TN00000001',
                        'amount' => 1000,
                    ],
//                    [
//                        'order_number' => 'T123456790',
//                        'amount' => 3456,
//                    ],
                ],
//                'refund' => [
//                    [
//                        'order_number' => 'T123456788',
//                        'amount' => 2311,
//                    ]
//                ]
            ],
            '8089016189' => [
                'master' => false,
                'authorize' => [
                    [
                        'order_number' => 'TO00000100',
                        'amount' => 1000,
                    ],
                    [
                        'order_number' => 'TO00000101',
                        'amount' => 2000,
                    ],
                ],
//                'refund' => [
//                    [
//                        'order_number' => 'T123456786',
//                        'amount' => 2456,
//                    ],
//                    [
//                        'order_number' => 'T123456788',
//                        'amount' => 2311,
//                    ],
//                    [
//                        'order_number' => 'T123456790',
//                        'amount' => 3456,
//                    ],
//                ]
            ],
        ]);
    }

    public function testAuthorizeGenerator()
    {
        $this->assertTrue($this->generator->generateFile(1)->isFile());
    }
}
