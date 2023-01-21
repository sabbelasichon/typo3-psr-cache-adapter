<?php
declare(strict_types=1);


namespace Ssch\Cache;


use InvalidArgumentException;
use Psr\Cache\CacheItemInterface;
use DateInterval;
use DateTime;
use DateTimeInterface;
use TypeError;
final class CacheItem implements CacheItemInterface
{
    private string $key;

    /**
     * @var mixed
     */
    private $value;

    private bool $isHit;

    private ?float $expiry = null;

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

    public function get()
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->isHit;
    }

    public function set($value): CacheItemInterface
    {
        $this->value = $value;

        return $this;
    }

    public function expiresAt($expiration): CacheItemInterface
    {
        if ($expiration === null) {
            $this->expiry = null;
        } elseif ($expiration instanceof DateTimeInterface) {
            $this->expiry = (float) $expiration->format('U.u');
        } else {
            throw new InvalidArgumentException(sprintf('Expiration date must be null or a DateTimeInterface, "%s" given.', get_debug_type($expiration)));
        }

        return $this;
    }

    public function expiresAfter($time): CacheItemInterface
    {
        if ($time === null) {
            $this->expiry = null;
        } elseif ($time instanceof DateInterval) {
            $this->expiry = microtime(true) + DateTime::createFromFormat('U', '0')->add($time)->format('U.u');
        } elseif (is_int($time)) {
            $this->expiry = $time + microtime(true);
        } else {
            throw new InvalidArgumentException(sprintf('Expiration date must be an integer, a DateInterval or null, "%s" given.', get_debug_type($time)));
        }

        return $this;
    }



    /**
     * @internal
     */
    public function getExpiry(): ?float
    {
        return $this->expiry;
    }
}