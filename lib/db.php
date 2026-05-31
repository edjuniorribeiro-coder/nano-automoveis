<?php
/** Conexão PDO com MySQL */
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $cfg = config('db');
    $dsn = "mysql:host={$cfg['host']};dbname={$cfg['name']};charset={$cfg['charset']}";
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetchOne(string $sql, array $params = []): ?array {
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}

function fetchAll(string $sql, array $params = []): array {
    return q($sql, $params)->fetchAll();
}

function insertGetId(string $table, array $data): int {
    $cols = implode(',', array_keys($data));
    $place = implode(',', array_map(fn($k) => ":$k", array_keys($data)));
    q("INSERT INTO $table ($cols) VALUES ($place)", $data);
    return (int) db()->lastInsertId();
}

function updateRow(string $table, array $data, int $id): void {
    $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($data)));
    $data['id'] = $id;
    q("UPDATE $table SET $set WHERE id = :id", $data);
}

function deleteRow(string $table, int $id): void {
    q("DELETE FROM $table WHERE id = :id", ['id' => $id]);
}
