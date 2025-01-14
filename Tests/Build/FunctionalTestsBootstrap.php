<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Testbase;

/**
 * Boilerplate for a functional test phpunit boostrap file.
 *
 * This file is loosely maintained within TYPO3 testing-framework, extensions
 * are encouraged to not use it directly, but to copy it to an own place,
 * usually in parallel to a FunctionalTests.xml file.
 *
 * This file is defined in FunctionalTests.xml and called by phpunit
 * before instantiating the test suites.
 */
(static function () {
    $testbase = new Testbase();
    $testbase->defineOriginalRootPath();
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/tests');
    $testbase->createDirectory(ORIGINAL_ROOT . 'typo3temp/var/transient');
})();
