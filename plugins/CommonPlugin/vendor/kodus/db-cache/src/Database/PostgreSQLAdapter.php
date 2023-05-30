<?php

namespace Kodus\Cache\Database;

use function count;
use PDO;
use function implode;

class PostgreSQLAdapter extends Adapter
{
    protected function createTable(): void
    {
        $this->unsafeExecute(
            $this->prepare(
                "CREATE TABLE {$this->table_name} (\n"
                . "  key CHARACTER VARYING NOT NULL PRIMARY KEY,\n"
                . "  data BYTEA,\n"
                . "  expires BIGINT\n"
                . ")"
            )
        );

        $this->unsafeExecute(
            $this->prepare(
                "CREATE INDEX {$this->table_name}_expires_index ON {$this->table_name} USING BTREE (expires);"
            )
        );
    }

    public function select(string $key): ?CacheEntry
    {
        $statement = $this->prepare("SELECT * FROM {$this->table_name} WHERE key = :key");

        $statement->bindValue("key", $key, PDO::PARAM_STR);

        $result = $this->fetch($statement);

        return $result[0] ?? null;
    }

    public function upsert(array $values, int $expires): void
    {
        $placeholders = [];

        for ($index = 0; $index < count($values); $index++) {
            $placeholders[] = "(:key_{$index}, :data_{$index}, :expires)";
        }

        $statement = $this->prepare(
            "INSERT INTO {$this->table_name} (key, data, expires)"
            . " VALUES " . implode(", ", $placeholders)
            . " ON CONFLICT (key) DO UPDATE SET data=EXCLUDED.data, expires=EXCLUDED.expires"
        );

        $statement->bindValue("expires", $expires);

        $index = 0;

        foreach ($values as $key => $data) {
            $statement->bindValue("key_{$index}", $key, PDO::PARAM_STR);
            $statement->bindValue("data_{$index}", $data, PDO::PARAM_LOB);

            $index += 1;
        }

        $this->execute($statement);
    }

    public function delete(string $key): void
    {
        $statement = $this->prepare("DELETE FROM {$this->table_name} WHERE key = :key");

        $statement->bindValue("key", $key, PDO::PARAM_STR);

        $this->execute($statement);
    }

    public function deleteExpired(int $now): void
    {
        $statement = $this->prepare("DELETE FROM {$this->table_name} WHERE :now >= expires");

        $statement->bindValue("now", $now, PDO::PARAM_INT);

        $this->execute($statement);
    }

    /**
     * @param string[] $keys
     *
     * @return CacheEntry[]
     */
    public function selectMultiple(array $keys): array
    {
        $placeholders = [];

        for ($index=0; $index<count($keys); $index++) {
            $placeholders[] = ":key_{$index}";
        }

        $statement = $this->prepare(
            "SELECT * FROM {$this->table_name} WHERE key IN (" . implode(", ", $placeholders) . ")"
        );

        foreach ($keys as $index => $key) {
            $statement->bindValue("key_{$index}", $key, PDO::PARAM_STR);
        }

        return $this->fetch($statement);
    }

    public function deleteMultiple(array $keys): void
    {
        $placeholders = [];

        for ($index=0; $index<count($keys); $index++) {
            $placeholders[] = ":key_{$index}";
        }

        $statement = $this->prepare(
            "DELETE FROM {$this->table_name} WHERE key IN (" . implode(", ", $placeholders) . ")"
        );

        foreach ($keys as $index => $key) {
            $statement->bindValue("key_{$index}", $key, PDO::PARAM_STR);
        }

        $this->execute($statement);
    }

    public function truncate(): void
    {
        $this->execute(
            $this->prepare("TRUNCATE TABLE {$this->table_name}")
        );
    }
}
