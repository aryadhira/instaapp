<?php

namespace App\DB;

use PDO;
use PDOException;

class DBConnector
{
    private static ?PDO $pdoInstance = null;

    private function __construct() {}

    private function __clone() {}

    public static function getConnection(): PDO
    {
        if (self::$pdoInstance === null) {

            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $name = $_ENV['DB_NAME'] ?? 'instaapp_db';
            $user = $_ENV['DB_USER'] ?? 'postgres';
            $password = $_ENV['DB_PASSWORD'] ?? 'password';

            $dsn = "pgsql:host={$host};dbname={$name};user={$user};password={$password}";

            try {
                $pdo = new PDO($dsn);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                self::$pdoInstance = $pdo;
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$pdoInstance;
    }
}
