<?php

namespace Kodus\Cache\Database;

use function implode;
use PDO;
use PDOException;
use PDOStatement;

abstract class Adapter
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var string
     */
    protected $table_name;

    public function __construct(PDO $pdo, string $table_name)
    {
        $this->pdo = $pdo;
        $this->table_name = $table_name;
    }

    abstract public function select(string $key): ?CacheEntry;

    abstract public function delete(string $key): void;

    /**
     * @param string[] $keys
     *
     * @return CacheEntry[]
     */
    abstract public function selectMultiple(array $keys): array;

    abstract public function deleteMultiple(array $keys): void;

    abstract public function upsert(array $values, int $expires): void;

    abstract public function truncate(): void;

    abstract public function deleteExpired(int $now): void;

    abstract protected function createTable(): void;

    protected function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    /**
     * @param PDOStatement $statement
     *
     * @return CacheEntry[]
     */
    protected function fetch(PDOStatement $statement): array
    {
        $rows = $this->execute($statement)->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $row) {
            $result[] = new CacheEntry(
                $row["key"],
                is_resource($row["data"])
                    ? stream_get_contents($row["data"])
                    : $row["data"],
                $row["expires"]);
        }

        return $result;
    }

    protected function execute(PDOStatement $statement): PDOStatement
    {
        try {
            $this->unsafeExecute($statement);
        } catch (PDOException $error) {
            // TODO only retry if the error is a missing table error
            $this->createTable();

            $this->unsafeExecute($statement);
        }

        return $statement;
    }

    protected function unsafeExecute(PDOStatement $statement): void
    {
        if ($statement->execute() !== true) {
            throw new PDOException(implode(" ", $statement->errorInfo()));
        }
    }
}
