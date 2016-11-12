# JSON Guard Cached

A json-guard decorator to enable schema caching.  Works with any PSR-6 cache implementation.

This adapter is experimental until it proves to be worth using.

## Install

Via Composer

``` bash
$ composer require yuloh/json-guard-cached
```

## Usage

``` php
use Cache\Adapter\Redis\RedisCachePool;
use League\JsonGuard\Dereferencer;
use League\JsonGuard\Cached\CachedDereferencer;

$client = new Redis();
$client->connect('127.0.0.1', 6379);
$cache = new RedisCachePool($client);

$deref = new Dereferencer();
$deref = new CachedDereferencer($cache, $deref);

$schema = $deref->dereference('file://my-schema.json');
```

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
