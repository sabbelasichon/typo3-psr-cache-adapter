<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Adapter;

use Psr\SimpleCache\CacheInterface;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithPsr16Cache;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class Psr16CacheAdapterTest extends FunctionalTestCase
{
    protected $testExtensionsToLoad = [
        'typo3conf/ext/typo3_psr_cache_adapter',
        'typo3conf/ext/typo3_psr_cache_adapter/Tests/Functional/Fixtures/Extensions/typo3_psr_cache_adapter_test',
    ];

    private CacheInterface $cacheAdapter;

    private ServiceWithPsr16Cache $serviceWithPsr16Cache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cacheAdapter = $this->get('cache.psr16.typo3_psr_cache_adapter_test');
        $this->serviceWithPsr16Cache = $this->get(ServiceWithPsr16Cache::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cacheAdapter->delete(ServiceWithPsr16Cache::CACHE_ITEM_KEY);
    }

    public function testThatFirstCalculationCreatesCacheEntry(): void
    {
        $this->serviceWithPsr16Cache->calculate();
        self::assertTrue($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
        self::assertSame(
            md5(ServiceWithPsr16Cache::CACHE_VALUE),
            $this->cacheAdapter->get(ServiceWithPsr16Cache::CACHE_ITEM_KEY)
        );
    }

    public function testThatGetItemsReturnsCorrectResults(): void
    {
        $this->serviceWithPsr16Cache->calculate();
        $items = $this->cacheAdapter->getMultiple([ServiceWithPsr16Cache::CACHE_ITEM_KEY]);

        foreach ($items as $item) {
            self::assertSame(md5(ServiceWithPsr16Cache::CACHE_VALUE), $item);
        }
    }

    public function testThatCacheIsTruncatedCorrectly(): void
    {
        $this->serviceWithPsr16Cache->calculate();
        self::assertTrue($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
        $this->cacheAdapter->clear();
        self::assertFalse($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
    }

    public function testThatDeletingItemsIsSuccessful(): void
    {
        $this->serviceWithPsr16Cache->calculate();
        self::assertTrue($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
        $this->cacheAdapter->deleteMultiple([ServiceWithPsr16Cache::CACHE_ITEM_KEY]);
        self::assertFalse($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
    }

//    public function testThatLifetimeIsCorrectlySet(): void
//    {
//        $this->serviceWithPsr16Cache->calculate(1);
//        sleep(2);
//        self::assertFalse($this->cacheAdapter->has(ServiceWithPsr16Cache::CACHE_ITEM_KEY));
//    }
}
