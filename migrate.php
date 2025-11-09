<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

use App\DB\DBConnector;

$migrationDir = __DIR__ . '/migrations';
$migrationTable = 'migrations_log'; // Table to track executed migrations

try {
    // Get the shared PDO instance from the connector class
    $pdo = DBConnector::getConnection();
    echo "Connected to PostgreSQL database successfully.\n";
} catch (\Exception $e) {
    // Catch any connection failures thrown by the Connector
    die("Database connection failed: " . $e->getMessage() . "\n");
}

try {
    // PostgreSQL uses SERIAL for auto-incrementing integers
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS {$migrationTable} (
            id SERIAL PRIMARY KEY, 
            migration_name VARCHAR(255) UNIQUE NOT NULL,
            executed_at TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
        );
    ");
} catch (PDOException $e) {
    die("Failed to create migration log table: " . $e->getMessage() . "\n");
}


// Get Executed Migrations
$stmt = $pdo->query("SELECT migration_name FROM {$migrationTable}");
$executedMigrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Find and Execute New Migrations
$files = glob($migrationDir . '/*.php');
sort($files);

foreach ($files as $filePath) {
    $fileName = basename($filePath);
    $fileClassName = pathinfo($fileName, PATHINFO_FILENAME);

    if (in_array($fileName, $executedMigrations)) {
        echo "Skipping migration: {$fileName} (Already executed)\n";
        continue;
    }

    echo "Running migration: {$fileName}...\n";

    // Convert filename to proper class name by converting snake_case to PascalCase
    $classNameParts = explode('_', $fileClassName);
    $className = implode('', array_map('ucfirst', $classNameParts));

    require_once $filePath;
    $migration = new $className($pdo); 

    // Execute the UP method
    $sql = $migration->up();
    $pdo->beginTransaction();
    try {
        $pdo->exec($sql);

        // Log successful execution
        $stmt = $pdo->prepare("INSERT INTO {$migrationTable} (migration_name) VALUES (?)");
        $stmt->execute([$fileName]);

        $pdo->commit();
        echo "Migration {$fileName} executed successfully.\n";
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error running migration {$fileName}: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "All migrations finished.\n";
