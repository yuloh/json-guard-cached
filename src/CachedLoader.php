<?php

namespace League\JsonGuard\Cached;

use League\JsonGuard\Loader;
use Psr\Cache\CacheItemPoolInterface;

class CachedLoader implements Loader
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var \League\JsonGuard\Loader
     */
    private $loader;

    /**
     * @param CacheItemPoolInterface $cache
     * @param Loader                 $loader
     */
    public function __construct(CacheItemPoolInterface $cache, Loader $loader)
    {
        $this->cache  = $cache;
        $this->loader = $loader;
    }

    /**
     * Load the json schema from the given path.
     *
     * @param string $path The path to load, without the protocol.
     *
     * @return object The object resulting from a json_decode of the loaded path.
     * @throws \League\JsonGuard\Exceptions\SchemaLoadingException
     */
    public function load($path)
    {
        $item = $this->cache->getItem(sha1($path));

        if ($item->isHit()) {
            return $item->get();
        }

        $schema = $this->loader->load($path);
        $item->set($schema);
        $this->cache->save($item);

        return $schema;
    }
}
