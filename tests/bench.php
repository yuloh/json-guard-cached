<?php

use Cache\Adapter\Redis\RedisCachePool;
use League\JsonGuard\Cached\CachedDereferencer;

require __DIR__ . '/../vendor/autoload.php';

$deref = new \League\JsonGuard\Dereferencer();

$start = microtime(true);

$deref->dereference('http://json-schema.org/schema');
$deref->dereference('http://jsonapi.org/schema');

$end = microtime(true);

$total = $end - $start;

echo 'STD TIME: ' . $total . PHP_EOL;


// -----------------------------

$client = new \Redis();
$client->connect('127.0.0.1', 6379);
$cache = new RedisCachePool($client);
$deref = new CachedDereferencer($cache, $deref);

$start = microtime(true);

$deref->dereference('http://json-schema.org/schema');
$deref->dereference('http://jsonapi.org/schema');

$end = microtime(true);

$total = $end - $start;

echo 'CACHED TIME: ' . $total . PHP_EOL;
