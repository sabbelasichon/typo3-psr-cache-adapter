<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Adapter;

use Psr\SimpleCache\CacheInterface;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

final class Psr16Adapter implements CacheInterface
{
    private FrontendInterface $cache;

    public function __construct(FrontendInterface $cache)
    {
        $this->cache = $cache;
    }

    public function get($key, $default = null): mixed
    {
        $result = $this->cache->get($this->hash($key));

        if ($result === false) {
            return $default;
        }

        return $result;
    }

    public function set($key, $value, $ttl = null): bool
    {
        if ($ttl instanceof \DateInterval) {
            $lifetime = $this->calculateLifetimeFromDateInterval($ttl);
        } else {
            $lifetime = $ttl;
        }

        $this->cache->set($this->hash($key), $value, [], $lifetime);

        return true;
    }

    public function delete($key): bool
    {
        return $this->cache->remove($this->hash($key));
    }

    public function clear(): bool
    {
        $this->cache->flush();

        return true;
    }

    public function getMultiple($keys, $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        $allSet = true;
        if ($ttl instanceof \DateInterval) {
            $lifetime = $this->calculateLifetimeFromDateInterval($ttl);
        } else {
            $lifetime = $ttl;
        }
        foreach ($values as $key => $value) {
            $allSet = $this->set($key, $value, $lifetime) && $allSet;
        }

        return $allSet;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function has($key): bool
    {
        return $this->cache->has($this->hash($key));
    }

    private function calculateLifetimeFromDateInterval(\DateInterval $ttl): int
    {
        return ((int) $ttl->format('a')) * 86400
            + $ttl->h * 3600
            + $ttl->m * 60
            + $ttl->s
        ;
    }

    private function hash(string $key): string
    {
        return sha1($key);
    }
}
