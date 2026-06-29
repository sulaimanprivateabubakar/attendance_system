<?php
// app/core/Database.php

/**
 * Database – singleton PDO wrapper
 *
 * Usage:
 *   $db  = Database::getInstance();
 *   $row = $db->single("SELECT * FROM users WHERE id = ?", [1]);
 *   $all = $db->all("SELECT * FROM students");
 *   $id  = $db->insert("INSERT INTO users (name) VALUES (?)", ['Alice']);
 *   $n   = $db->execute("UPDATE users SET name=? WHERE id=?", ['Bob', 1]);
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    // ── Construction ────────────────────────────────────────────────────────

    private function __construct()
    {
        $config = require APP_PATH . '/config/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        } catch (PDOException $e) {
            // Never expose credentials in output
            error_log('DB connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed. Check logs for details.');
        }
    }

    /** Prevent cloning of the singleton. */
    private function __clone() {}

    // ── Singleton accessor ───────────────────────────────────────────────────

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ── Query helpers ────────────────────────────────────────────────────────

    /**
     * Prepare and execute a statement, return the PDOStatement.
     */
    private function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row as an associative array, or null if not found.
     */
    public function single(string $sql, array $params = []): ?array
    {
        $row = $this->run($sql, $params)->fetch();
        return $row !== false ? $row : null;
    }

    /**
     * Fetch all matching rows as an array of associative arrays.
     */
    public function all(string $sql, array $params = []): array
    {
        return $this->run($sql, $params)->fetchAll();
    }

    /**
     * Execute an INSERT and return the last inserted ID.
     */
    public function insert(string $sql, array $params = []): int|string
    {
        $this->run($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Execute UPDATE / DELETE and return affected row count.
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->run($sql, $params)->rowCount();
    }

    /**
     * Fetch a single column from the first row (useful for COUNT, etc.).
     */
    public function scalar(string $sql, array $params = []): mixed
    {
        $row = $this->run($sql, $params)->fetch(PDO::FETCH_NUM);
        return $row ? $row[0] : null;
    }

    // ── Transactions ─────────────────────────────────────────────────────────

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollback(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * Run a callable inside a transaction; rolls back on any exception.
     *
     * @throws Throwable
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();
        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    // ── Utility ──────────────────────────────────────────────────────────────

    /** Expose PDO directly for edge cases (e.g. LIKE bindings). */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
