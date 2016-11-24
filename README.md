Installation
-------------

```shell
$    composer require vb-payment/esunacq-creditcard-authorize
```

### Use AuthorizeGenerator generate txt for esunacq creditcard authorize or refund

```php
<?php
    use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\AuthorizeGenerator;

    $generator = new AuthorizeGenerator('{廠商代碼}', [
    '{商店代碼}' => [
        'master' => true,	
        'authorize' => [[
            'order_number' => '{授權訂單編號}',
            'amount' => {授權訂單金額},
        ]],
        'refund' => [[
            'order_number' => '{退貨訂單編號}',
            'amount' => {退貨訂單金額},
        ]]
            ]
    ], '{產出檔案資料夾(絕對路徑)}');

    /**
     * 1. $num 是每天產出檔案的流水號 default: 1, length: 2
     * 2. 檔案會產生在一開始設定的資料夾中
     * 3. 檔案命名：{廠商代碼}${Ymd}{流水號%02d}I.txt (ex: D610$2016112301I.txt)
     */
    $generator->generateFile($num);

```

### Use AuthorizeParser parser response txt file

```php
<?php
    use VeryBuy\Payment\EsunBank\CreditCard\Order\Authorize\AuthorizeParser;

    $collection = (new AuthorizeParser({實際檔案路徑}))->getParsedCollection();

    $result = $collection->dump();

```
