<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = db();
    echo 'MySQL connection OK';
} catch (Throwable $e) {
    echo 'Connection failed: ' . $e->getMessage();
}