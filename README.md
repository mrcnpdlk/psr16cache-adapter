 # PSR-16 Cache Adapter
 
 ## Instalation
 ```bash
composer require mrcnpdlk/psr16cache-adapter
```
 ## Basic usage
 
 ```php
 $oInstanceCacheFile = new \phpFastCache\Helper\Psr16Adapter(
     'files',
     [
         "host"                => null, // default localhost
         "port"                => null, // default 6379
         'defaultTtl'          => 3600 * 24, // 24h
         'ignoreSymfonyNotice' => true,
     ]);
 $oInstanceLogger    = new \Monolog\Logger('name_of_my_logger');
 $oInstanceLogger->pushHandler(new \Monolog\Handler\ErrorLogHandler(
         \Monolog\Handler\ErrorLogHandler::OPERATING_SYSTEM,
         \Psr\Log\LogLevel::DEBUG
     )
 );
 
 $oCacheAdapter = new \mrcnpdlk\Psr16Cache\Adapter($oInstanceCacheFile, $oInstanceLogger);
 $oCacheAdapter->setHashSalt(['some_hash', __DIR__]);
 
 $res = $oCacheAdapter->useCache(
     function () {
         return '1';
     },
     ['key1', 'key2'],
     10
 );
 
 var_dump($res);
 ```
 
 Response
 ```text
[2017-10-16 19:17:41] name_of_my_logger.DEBUG: CACHE [ffe7efd40ef900c95726a859aa28e048]: old, reset [] []
string(1) "1"

[2017-10-16 19:17:44] name_of_my_logger.DEBUG: CACHE [ffe7efd40ef900c95726a859aa28e048]: getting from cache [] []
string(1) "1"
```

## Running the tests

```bash
./vendor/bin/phpunit
```

## Authors

* **Marcin Pudełek** - *Initial work* - [mrcnpdlk](https://github.com/mrcnpdlk)

See also the list of [contributors](https://github.com/mrcnpdlk/validator/graphs/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/mrcnpdlk/psr16cache-adapter/blob/master/LICENSE) file for details
