<?php
// migrate/includes/db.php

$config_file = __DIR__ . '/../config.php';

if (file_exists($config_file)) {
    require_once $config_file;
} else {
    // If config doesn't exist, we might be in the middle of installation
    // but usually this file shouldn't be included if not installed.
}

function get_db_connection() {
    if (!defined('DB_HOST')) {
        return null;
    }

    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // In a real production app, you might want to log this instead of throwing
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
