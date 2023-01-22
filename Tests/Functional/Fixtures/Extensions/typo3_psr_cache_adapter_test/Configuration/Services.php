<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Ssch\Cache\Adapter\Psr16Adapter;
use Ssch\Cache\Adapter\Psr6Adapter;
use Ssch\Cache\Factory\Psr16Factory;
use Ssch\Cache\Factory\Psr6Factory;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithPsr16Cache;
use Ssch\Cache\Tests\Functional\Fixtures\Extensions\typo3_psr_cache_adapter_test\Classes\Service\ServiceWithPsr6Cache;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->set('cache.psr6.typo3_psr_cache_adapter_test', Psr6Adapter::class)
        ->factory([service(Psr6Factory::class), 'create'])
        ->args(['typo3_psr_cache_adapter_test']);
    $services->set(ServiceWithPsr6Cache::class)
        ->args([service('cache.psr6.typo3_psr_cache_adapter_test')]);

    $services->set('cache.psr16.typo3_psr_cache_adapter_test', Psr16Adapter::class)
        ->factory([service(Psr16Factory::class), 'create'])
        ->args(['typo3_psr_cache_adapter_test']);
    $services->set(ServiceWithPsr16Cache::class)
        ->args([service('cache.psr16.typo3_psr_cache_adapter_test')]);
};
