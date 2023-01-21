<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Cache\Adapter\Psr6Adapter;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithCache;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->set('cache.typo3_psr_cache_adapter_test', FrontendInterface::class)
        ->factory([service(CacheManager::class), 'getCache'])
        ->args(['typo3_psr_cache_adapter_test']);
    $services->set('cache.psr6.typo3_psr_cache_adapter_test', Psr6Adapter::class)
        ->args([service('cache.typo3_psr_cache_adapter_test')]);
    $services->set(ServiceWithCache::class)
        ->args([service('cache.psr6.typo3_psr_cache_adapter_test')]);
};
