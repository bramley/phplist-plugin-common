<?php

declare(strict_types=1);

namespace Kodus\Cache;

use DateInterval;
use Kodus\Cache\Database\Adapter;
use Kodus\Cache\Database\InvalidArgumentException;
use Kodus\Cache\Database\MySQLAdapter;
use Kodus\Cache\Database\PostgreSQLAdapter;
use PDO;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Traversable;
use function array_fill_keys;
use function date_create_from_format;
use function get_class;
use function gettype;
use function is_array;
use function is_int;
use function iterator_to_array;
use function unserialize;

/**
 * This class implements a PSR-16 cache backed by an SQL database.
 *
 * Make sure your schedule an e.g. nightly call to {@see cleanExpired()}.
 */
class DatabaseCache implements CacheInterface
{
    /**
     * @var string control characters for keys, reserved by PSR-16
     */
    const PSR16_RESERVED = '/\{|\}|\(|\)|\/|\\\\|\@|\:/u';

    /**
     * @var int
     */
    private $default_ttl;

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @param PDO    $pdo         PDO database connection
     * @param string $table_name
     * @param int    $default_ttl default time-to-live (in seconds)
     */
    public function __construct(PDO $pdo, string $table_name, int $default_ttl)
    {
        $this->adapter = $this->createAdapter($pdo, $table_name);
        $this->default_ttl = $default_ttl;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);

        $entry = $this->adapter->select($key);

        if ($entry === null) {
            return $default; // entry not found
        }

        if ($this->getTime() >= $entry->expires) {
            return $default; // entry expired
        }

        if ($entry->data === 'b:0;') {
            return false; // because we can't otherwise distinguish a FALSE return-value from `unserialize()`
        }

        $value = @unserialize($entry->data);

        if ($value === false) {
            return $default; // `unserialize()` failed
        }

        return $value;
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $this->validateKey($key);

        $data = serialize($value);

        $this->adapter->upsert([$key => $data], $this->expirationFromTTL($ttl));

        return true;
    }

    public function delete(string $key): bool
    {
        $this->validateKey($key);

        $this->adapter->delete($key);

        return true;
    }

    public function clear(): bool
    {
        $this->adapter->truncate();

        return true;
    }

    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        if (! is_array($keys)) {
            if ($keys instanceof Traversable) {
                $keys = iterator_to_array($keys, false);
            } else {
                throw new InvalidArgumentException("keys must be either of type array or Traversable");
            }
        }

        foreach ($keys as $key) {
            $this->validateKey($key);
        }

        $result = $this->adapter->selectMultiple($keys);

        $values = array_fill_keys($keys, $default);

        foreach ($result as $entry) {
            if ($entry->data === 'b:0;') {
                $value = false; // because we can't otherwise distinguish a FALSE return-value from `unserialize()`
            } else {
                $value = @unserialize($entry->data);

                if ($value === false) {
                    $value = $default;
                }
            }

            $values[$entry->key] = $value;
        }

        return $values;
    }

    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        if (! is_array($values) && ! $values instanceof Traversable) {
            throw new InvalidArgumentException("keys must be either of type array or Traversable");
        }

        $data = [];

        foreach ($values as $key => $value) {
            if (! is_int($key)) {
                $this->validateKey($key);
            }

            $data[$key] = serialize($value);
        }

        $this->adapter->upsert($data, $this->expirationFromTTL($ttl));

        return true;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        if (! is_array($keys)) {
            if ($keys instanceof Traversable) {
                $keys = iterator_to_array($keys, false);
            } else {
                throw new InvalidArgumentException("keys must be either of type array or Traversable");
            }
        }

        foreach ($keys as $key) {
            $this->validateKey($key);
        }

        if (count($keys)) {
            $this->adapter->deleteMultiple($keys);
        }

        return true;
    }

    public function has(string $key): bool
    {
        return $this->get($key, $this) !== $this;
    }

    /**
     * Clean up expired cache-files.
     *
     * This method is outside the scope of the PSR-16 cache concept, and is specific to
     * this implementation.
     *
     * In scenarios with dynamic keys (such as Session IDs) you should call this method
     * periodically - for example from a scheduled daily cron-job.
     *
     * @return void
     */
    public function cleanExpired()
    {
        $this->adapter->deleteExpired($this->getTime());
    }

    /**
     * @return int current timestamp
     */
    protected function getTime()
    {
        return time();
    }

    private function validateKey($key): void
    {
        if (! is_string($key)) {
            $type = is_object($key) ? get_class($key) : gettype($key);

            throw new InvalidArgumentException("invalid key type: {$type} given");
        }

        if ($key === "") {
            throw new InvalidArgumentException("invalid key: empty string given");
        }

        if (preg_match(self::PSR16_RESERVED, $key, $match) === 1) {
            throw new InvalidArgumentException("invalid character in key: {$match[0]}");
        }
    }

    private function createAdapter(PDO $pdo, string $table_name): Adapter
    {
        $driver_name = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        switch ($driver_name) {
            case "pgsql":
                return new PostgreSQLAdapter($pdo, $table_name);
            case "mysql":
                return new MySQLAdapter($pdo, $table_name);
        }

        throw new RuntimeException("Unsupported PDO driver: {$driver_name}");
    }

    private function expirationFromTTL($ttl): int
    {
        /**
         * @var int $expires
         */

        if (is_int($ttl)) {
            return $this->getTime() + $ttl;
        } elseif ($ttl instanceof DateInterval) {
            return date_create_from_format("U", (string) $this->getTime())->add($ttl)->getTimestamp();
        } elseif ($ttl === null) {
            return $this->getTime() + $this->default_ttl;
        } else {
            $type = is_object($ttl) ? get_class($ttl) : gettype($ttl);

            throw new InvalidArgumentException("invalid TTL type: {$type}");
        }
    }
}
