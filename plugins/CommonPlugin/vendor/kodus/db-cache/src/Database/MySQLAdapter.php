<?php

namespace Kodus\Cache\Database;

use function count;
use PDO;
use function implode;

class MySQLAdapter extends Adapter
{
    protected function createTable(): void
    {
        $this->unsafeExecute(
            $this->prepare(
                "CREATE TABLE IF NOT EXISTS `{$this->table_name}` (\n"
	            . "  `key` VARCHAR(512) NOT NULL COLLATE 'latin1_bin',\n"
	            . "  `data` MEDIUMBLOB NOT NULL,\n"
	            . "  `expires` BIGINT NOT NULL,\n"
	            . "   PRIMARY KEY (`key`),\n"
                . "   INDEX `expires` (`expires`)\n"
                . ")"
            )
        );
    }

    public function select(string $key): ?CacheEntry
    {
        $statement = $this->prepare("SELECT * FROM `{$this->table_name}` WHERE `key` = :key");

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
            "REPLACE INTO `{$this->table_name}` (`key`, `data`, `expires`)"
            . " VALUES " . implode(", ", $placeholders)
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
        $statement = $this->prepare("DELETE FROM `{$this->table_name}` WHERE `key` = :key");

        $statement->bindValue("key", $key, PDO::PARAM_STR);

        $this->execute($statement);
    }

    public function deleteExpired(int $now): void
    {
        $statement = $this->prepare("DELETE FROM `{$this->table_name}` WHERE :now >= `expires`");

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
            "SELECT * FROM `{$this->table_name}` WHERE `key` IN (" . implode(", ", $placeholders) . ")"
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
            "DELETE FROM `{$this->table_name}` WHERE `key` IN (" . implode(", ", $placeholders) . ")"
        );

        foreach ($keys as $index => $key) {
            $statement->bindValue("key_{$index}", $key, PDO::PARAM_STR);
        }

        $this->execute($statement);
    }

    public function truncate(): void
    {
        $this->execute(
            $this->prepare("TRUNCATE TABLE `{$this->table_name}`")
        );
    }
}
