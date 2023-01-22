<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Factory;

use Psr\Cache\CacheItemPoolInterface;
use Ssch\Cache\Adapter\Psr6Adapter;
use TYPO3\CMS\Core\Cache\CacheManager;

final class Psr6Factory
{
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function create(string $identifier): CacheItemPoolInterface
    {
        return new Psr6Adapter($this->cacheManager->getCache($identifier));
    }
}
