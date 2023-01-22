# PSR Cache Adapters
Provide a PSR-6 and PSR-16 compatible cache adapter for the TYPO3 Caching Framework.

## Usage

To ease the creation of a PSR-6 or PSR-16 compatible cache object the extension ships with two factories.
One for PSR-6 and one for PSR-16.  
In order create either a PSR-6 or a PSR-16 compatible cache object you can configure it via Symfony DI

```php
use Ssch\Cache\Adapter\Psr6Adapter;
use Ssch\Cache\Factory\Psr6Factory;
use MyNamespace\MyExtensionKey\Service\MyService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->private()
        ->autoconfigure();

    $services->set('cache.psr6.typo3_psr_cache_adapter_test', Psr6Adapter::class)
        ->factory([service(Psr6Factory::class), 'create'])
        ->args(['typo3_psr_cache_adapter_test']);
    $services->set(MyService::class)
        ->args([service('cache.psr6.typo3_psr_cache_adapter_test')]);
};
```

Note the *typo3_psr_cache_adapter_test*. This is the cache identifier you have used to configure your TYPO3 Cache for the [TYPO3 Caching Framework](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/CachingFramework/Configuration/Index.html#cache-configurations).

