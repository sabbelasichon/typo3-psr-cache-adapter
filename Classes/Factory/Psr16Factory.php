<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Factory;

use Psr\SimpleCache\CacheInterface;
use Ssch\Cache\Adapter\Psr16Adapter;
use TYPO3\CMS\Core\Cache\CacheManager;

final class Psr16Factory
{
    private CacheManager $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function create(string $identifier): CacheInterface
    {
        return new Psr16Adapter($this->cacheManager->getCache($identifier));
    }
}
