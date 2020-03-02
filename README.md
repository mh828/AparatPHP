#AparatAPI Manual

## Introduction
AparatAPI is a php library for ease using of [Aparat website](https://aparat.com) API. 

[Aparat API resource](https://www.aparat.com/api)

## Getting Start
to use this library first you must include library class to your project,
thus best way is create an autoloader to automate that process.
```php
spl_autoload_register(function ($ns) {
    $path = str_replace('\\', '/', __DIR__ . '/Classes/' . $ns) . '.php';

    if (file_exists($path))
        include_once $path . '';
}, false, true);
```

# Available methods
    * login
    * profile
    * userBySearch
    * profileCategories
    * video
    * categoryVideos
    * videoByUser
    * videoBySearch

**Author:** Mahdi Hasanpour


[<img src="https://idpay.ir/icon-180.png" alt='Donate me' title='Donate me' width="75px" />](https://idpay.ir/mahdi-hasanpour)