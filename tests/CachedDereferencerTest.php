<?php

namespace League\JsonGuard\Cached;

use Cache\Adapter\PHPArray\ArrayCachePool;

class CachedDereferencerTest extends \PHPUnit_Framework_TestCase
{
    public function testCachedLoaderCachesTheSchema()
    {
        $cache        = new ArrayCachePool();
        $dereferencer = new CachedDereferencer($cache);

        $path   = 'file://' . __DIR__ . '/fixtures/schema.json';
        $schema = $dereferencer->dereference($path);

        $this->assertEquals($dereferencer->getDereferencer()->dereference($path), $schema);
        $this->assertTrue($cache->getItem(sha1($path))->isHit());
    }

    public function testCachedLoaderReturnsTheCachedSchema()
    {
        $cache        = new ArrayCachePool();
        $dereferencer = new CachedDereferencer($cache);

        $path = 'file://' . __DIR__ . '/fixtures/schema.json';
        $item = $cache->getItem(sha1($path));
        $item->set($cachedSchema = new \stdClass());
        $cache->save($item);
        $schema     = $dereferencer->dereference($path);
        $nextSchema = $dereferencer->dereference($path);

        $this->assertSame($cachedSchema, $schema);
        $this->assertSame($cachedSchema, $nextSchema);
    }
}

