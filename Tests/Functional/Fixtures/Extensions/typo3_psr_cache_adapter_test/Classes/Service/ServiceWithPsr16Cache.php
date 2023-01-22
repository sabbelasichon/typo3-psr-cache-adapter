<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service;

use Psr\SimpleCache\CacheInterface;

final class ServiceWithPsr16Cache
{
    public const CACHE_ITEM_KEY = 'foo-bar-baz';

    public const CACHE_VALUE = 'a-cache-value';

    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function calculate(int $lifetime = null): void
    {
        if (! $this->cache->has(self::CACHE_ITEM_KEY)) {
            $this->cache->set(self::CACHE_ITEM_KEY, md5(self::CACHE_VALUE), $lifetime);
        }
    }
}
