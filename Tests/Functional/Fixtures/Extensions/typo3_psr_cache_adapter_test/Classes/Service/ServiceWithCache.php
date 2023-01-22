<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service;

use Psr\Cache\CacheItemPoolInterface;

final class ServiceWithCache
{
    public const CACHE_ITEM_KEY = 'foo-bar-baz';

    public const CACHE_VALUE = 'a-cache-value';

    private CacheItemPoolInterface $cacheItemPool;

    public function __construct(CacheItemPoolInterface $cacheItemPool)
    {
        $this->cacheItemPool = $cacheItemPool;
    }

    public function calculate(int $lifetime = null)
    {
        $cacheItem = $this->cacheItemPool->getItem(self::CACHE_ITEM_KEY);
        if (! $cacheItem->isHit()) {
            $cacheItem->expiresAfter($lifetime);
            $cacheItem->set(md5(self::CACHE_VALUE));
            $this->cacheItemPool->save($cacheItem);
        }
    }
}
