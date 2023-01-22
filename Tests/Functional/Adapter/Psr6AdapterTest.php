<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Adapter;

use Psr\Cache\CacheItemPoolInterface;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithPsr6Cache;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class Psr6AdapterTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/typo3_psr_cache_adapter',
        'typo3conf/ext/typo3_psr_cache_adapter/Tests/Functional/Fixtures/Extensions/typo3_psr_cache_adapter_test',
    ];

    private CacheItemPoolInterface $cacheAdapter;

    private ServiceWithPsr6Cache $serviceWithPsr6Cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheAdapter = $this->get('cache.psr6.typo3_psr_cache_adapter_test');
        $this->serviceWithPsr6Cache = $this->get(ServiceWithPsr6Cache::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cacheAdapter->deleteItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY);
    }

    public function testThatFirstCalculationCreatesCacheEntry(): void
    {
        $this->serviceWithPsr6Cache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        self::assertSame(
            md5(ServiceWithPsr6Cache::CACHE_VALUE),
            $this->cacheAdapter->getItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY)->get()
        );
    }

    public function testThatGetItemsReturnsCorrectResults(): void
    {
        $this->serviceWithPsr6Cache->calculate();
        $items = $this->cacheAdapter->getItems([ServiceWithPsr6Cache::CACHE_ITEM_KEY]);

        foreach ($items as $item) {
            self::assertSame(md5(ServiceWithPsr6Cache::CACHE_VALUE), $item->get());
        }
    }

    public function testThatCacheIsTruncatedCorrectly(): void
    {
        $this->serviceWithPsr6Cache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        $this->cacheAdapter->clear();
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
    }

    public function testThatDeletingItemsIsSuccessful(): void
    {
        $this->serviceWithPsr6Cache->calculate();
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        $this->cacheAdapter->deleteItems([ServiceWithPsr6Cache::CACHE_ITEM_KEY]);
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
    }

    public function testThatLifetimeIsCorrectlySet(): void
    {
        $this->serviceWithPsr6Cache->calculate(1);
        sleep(2);
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
    }
}
