<?php

namespace League\JsonGuard\Cached;

use League\JsonGuard\Dereferencer;
use Psr\Cache\CacheItemPoolInterface;

/**
 * A caching decorator for the dereferencer.
 * Caches the initial schema (unless it's an object) and caches all external references.
 */
class CachedDereferencer extends Dereferencer
{
    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var \League\JsonGuard\Dereferencer
     */
    private $dereferencer;

    /**
     * @param CacheItemPoolInterface $cache
     * @param Dereferencer           $dereferencer
     */
    public function __construct(CacheItemPoolInterface $cache, Dereferencer $dereferencer = null)
    {
        $this->cache        = $cache;
        $this->dereferencer = $dereferencer ?: new Dereferencer();
        $this->cacheLoaders();
    }

    /**
     * Return the schema with all references resolved.
     *
     * @param string|object $schema Either a valid path like "http://json-schema.org/draft-03/schema#"
     *                              or the object resulting from a json_decode call.
     *
     * @return object
     */
    public function dereference($schema)
    {
        if (!is_string($schema)) {
            return $this->dereferencer->dereference($schema);
        }

        $item = $this->cache->getItem(sha1($schema));

        if ($item->isHit()) {
            return $item->get();
        }

        $schema = $this->dereferencer->dereference($schema);
        $item->set($schema);
        $this->cache->save($item);

        return $schema;
    }

    /**
     * @return Dereferencer
     */
    public function getDereferencer()
    {
        return $this->dereferencer;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Decorate all of the loaders with cached loaders.
     */
    private function cacheLoaders()
    {
        foreach ($this->dereferencer->getLoaders() as $prefix => $loader) {
            $cached = new CachedLoader($this->cache, $loader);
            $this->dereferencer->registerLoader($cached, $prefix);
        }
    }
}
