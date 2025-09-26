<?php

function getDb(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}

function run(string $sql, array $params = []): PDOStatement {
    $pdo = getDb();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function transaction(callable $fn) {
    $pdo = getDb();
    try {
        $pdo->beginTransaction();
        $result = $fn($pdo);
        $pdo->commit();
        return $result;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}


