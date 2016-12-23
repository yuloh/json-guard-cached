<?php

namespace League\JsonGuard\Cached;

use Cache\Adapter\PHPArray\ArrayCachePool;
use League\JsonGuard\Loaders\FileLoader;

class CachedLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testCachedLoaderCachesTheSchema()
    {
        $cache  = new ArrayCachePool();
        $loader = new CachedLoader($cache, $fileLoader = new FileLoader());

        $path   =  __DIR__ . '/fixtures/schema.json';
        $schema = $loader->load($path);

        $this->assertEquals($fileLoader->load($path), $schema);
        $this->assertTrue($cache->getItem(sha1($path))->isHit());
    }

    public function testCachedLoaderReturnsTheCachedSchema()
    {
        $cache  = new ArrayCachePool();
        $loader = new CachedLoader($cache, $fileLoader = new FileLoader());

        $path   =  __DIR__ . '/fixtures/schema.json';
        $item = $cache->getItem(sha1($path));
        $item->set($cachedSchema = new \stdClass());
        $cache->save($item);

        $schema = $loader->load($path);
        $nextSchema = $loader->load($path);

        $this->assertSame($cachedSchema, $schema);
        $this->assertSame($cachedSchema, $nextSchema);
    }
}
