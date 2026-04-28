<?php
namespace App\Config;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '5432';
            $db = getenv('DB_NAME') ?: 'gimnasio_db';
            $user = getenv('DB_USER') ?: 'gimnasio_user';
            $pass = getenv('DB_PASSWORD') ?: 'gimnasio_pass';
            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

            try {
                self::$connection = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Error conectando a la base de datos', 'detail' => $e->getMessage()]);
                exit;
            }
        }
        return self::$connection;
    }
}
