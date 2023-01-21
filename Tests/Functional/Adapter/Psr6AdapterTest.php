<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Adapter;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithCache;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class Psr6AdapterTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/typo3_psr_cache_adapter',
        'typo3conf/ext/typo3_psr_cache_adapter/Tests/Functional/Fixtures/Extensions/typo3_psr_cache_adapter_test',
    ];

    private CacheItemPoolInterface $cacheAdapter;

    private ServiceWithCache $serviceWithCache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheAdapter = $this->get('cache.psr6.typo3_psr_cache_adapter_test');
        $this->serviceWithCache = $this->get(ServiceWithCache::class);
    }

    public function testThatFirstCalculationCreatesCacheEntry(): void
    {
        $this->serviceWithCache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithCache::CACHE_ITEM_KEY));
        self::assertSame(
            md5(ServiceWithCache::CACHE_VALUE),
            $this->cacheAdapter->getItem(ServiceWithCache::CACHE_ITEM_KEY)->get()
        );
    }

    public function testThatGetItemsReturnsCorrectResults(): void
    {
        $this->serviceWithCache->calculate();
        $items = $this->cacheAdapter->getItems([ServiceWithCache::CACHE_ITEM_KEY]);
        self::assertInstanceOf(CacheItemInterface::class, $items[0]);
        self::assertSame(md5(ServiceWithCache::CACHE_VALUE), $items[0]->get());
    }

    public function testThatCacheIsTruncatedCorrectly(): void
    {
        $this->serviceWithCache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithCache::CACHE_ITEM_KEY));
        $this->cacheAdapter->clear();
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithCache::CACHE_ITEM_KEY));
    }

    public function testThatDeletingItemsIsSuccessful(): void
    {
        $this->serviceWithCache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithCache::CACHE_ITEM_KEY));
        $this->cacheAdapter->deleteItems([ServiceWithCache::CACHE_ITEM_KEY]);
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithCache::CACHE_ITEM_KEY));
    }
}
