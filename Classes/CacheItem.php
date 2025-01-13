<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_psr_cache_adapter" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Ssch\Cache;

use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;

final class CacheItem implements CacheItemInterface
{
    private string $key;

    /**
     * @var mixed
     */
    private $value;

    private bool $isHit;

    private ?int $expiry = null;

    /**
     * @param mixed $data
     */
    public function __construct(string $key, $data, bool $isHit)
    {
        $this->key = $key;
        $this->value = $data;
        $this->isHit = $isHit;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        if ($expiration === null) {
            $this->expiry = null;
        } elseif ($expiration instanceof \DateTimeInterface) {
            $this->expiry = $expiration->getTimestamp();
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expiration date must be null or an integer (Unix timestamp), "%s" given.',
                get_debug_type($expiration)
            ));
        }

        return $this;
    }

    public function expiresAfter(\DateInterval|int|null $time): static
    {
        if ($time === null) {
            $this->expiry = null;
        } elseif (is_int($time)) {
            $this->expiry = (int) ($GLOBALS['EXEC_TIME'] + $time);
        } elseif ($time instanceof \DateInterval) {
            $this->expiry = (new \DateTime('@0'))->add($time)
                ->getTimestamp() + $GLOBALS['EXEC_TIME'];
        } else {
            throw new InvalidArgumentException(sprintf(
                'Expiration date must be null, integer or DateInterval, "%s" given.',
                get_debug_type($time)
            ));
        }

        return $this;
    }

    /**
     * @internal
     */
    public function getExpiry(): ?int
    {
        return $this->expiry;
    }
}
