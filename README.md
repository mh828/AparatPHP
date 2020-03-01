# راهنمای استفاده از کتابخانه AparatAPI


#import classes
ابتدا لازم است که برای استفاده از کلاسهای کتابخانه یک autoloader تعریف کنیم که با استفاده از فانکشن spl_autoload_register این که کار به اسانی انجام میشود
```php
spl_autoload_register(function ($ns) {
    $path = str_replace('\\', '/', __DIR__ . '/Classes/' . $ns) . '.php';

    if (file_exists($path))
        include_once $path . '';
}, false, true);
```


**Author:** Mahdi Hasanpour
[Donate Me](https://idpay.ir/mahdi-hasanpour)