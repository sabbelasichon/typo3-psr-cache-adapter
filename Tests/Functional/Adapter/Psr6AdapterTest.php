<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache\Tests\Functional\Adapter;

use DateInterval;
use Psr\Cache\CacheItemPoolInterface;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithPsr6Cache;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class Psr6AdapterTest extends FunctionalTestCase
{
    private CacheItemPoolInterface $cacheAdapter;

    private ServiceWithPsr6Cache $serviceWithPsr6Cache;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'typo3conf/ext/typo3_psr_cache_adapter',
            'typo3conf/ext/typo3_psr_cache_adapter/Tests/Functional/Fixtures/Extensions/typo3_psr_cache_adapter_test',
        ];
        parent::setUp();
        $this->cacheAdapter = $this->get('cache.psr6.typo3_psr_cache_adapter_test');
        $this->serviceWithPsr6Cache = $this->get(ServiceWithPsr6Cache::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cacheAdapter->deleteItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY);
    }

    /**
     * @return array[]
     */
    public static function lifetimeProvider()
    {
        return [
            'null' => ['', null],
            'int' => ['', 1],
            'DateInterval' => ['', new \DateInterval('PT1S')],
        ];
    }

    /**
     * @dataProvider lifetimeProvider
     * @param DateInterval|int|null $input
     */
    public function testThatFirstCalculationCreatesCacheEntry(string $expected, $input): void
    {
        $this->cacheAdapter->clear();
        $this->serviceWithPsr6Cache->calculate($input);
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        self::assertSame(
            md5(ServiceWithPsr6Cache::CACHE_VALUE),
            $this->cacheAdapter->getItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY)->get()
        );
    }

    /**
     * @dataProvider lifetimeProvider
     * @param DateInterval|int|null $input
     */
    public function testThatGetItemsReturnsCorrectResults(string $expected, $input): void
    {
        $this->cacheAdapter->clear();
        $this->serviceWithPsr6Cache->calculate($input);
        $items = $this->cacheAdapter->getItems([ServiceWithPsr6Cache::CACHE_ITEM_KEY]);

        foreach ($items as $item) {
            self::assertSame(md5(ServiceWithPsr6Cache::CACHE_VALUE), $item->get());
        }
    }

    /**
     * @return array[]
     */
    public static function expiresAfterTimeProvider()
    {
        return [
            'int' => ['', 1],
            'DateInterval' => ['', new \DateInterval('PT1S')],
        ];
    }

    /**
     * @dataProvider expiresAfterTimeProvider
     * @param DateInterval|int|null $input
     */
    public function testThatLifetimeIsCorrectlySet(string $expected, $input): void
    {
        $this->cacheAdapter->clear();
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        $this->serviceWithPsr6Cache->calculate($input);
        self::assertTrue($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
        /*
        Expiry, when given as integer, is set as timestamp: $GLOBALS['EXEC_TIME'] + lifetime
        FileBackend calculates the expiry based on $GLOBALS['EXEC_TIME']:
        It is expired when: $expiryTime !== 0 && $expiryTime < $GLOBALS['EXEC_TIME'];
        Because we set and query the cache item in the same request $GLOBALS['EXEC_TIME'] is the same
        when setting the expiry time and when checking for its expiry in FileBackend.
        As a workaround we manipulate $GLOBALS['EXEC_TIME'] */
        $GLOBALS['EXEC_TIME'] = $GLOBALS['EXEC_TIME'] + 2;
        self::assertFalse($this->cacheAdapter->hasItem(ServiceWithPsr6Cache::CACHE_ITEM_KEY));
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
}
