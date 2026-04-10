<?php

declare(strict_types=1);

namespace App\Database;

use Dotenv\Dotenv;
use MongoDB\Client;
use MongoDB\Database;
use RuntimeException;

class Connection
{
    private static ?Client $client = null;
    private static ?Database $database = null;
    private static bool $envLoaded = false;

    public static function db(): Database
    {
        if (self::$database !== null) {
            return self::$database;
        }

        self::loadEnv();

        $uri = $_ENV['MONGODB_URI'] ?? null;
        $dbName = $_ENV['MONGODB_DB'] ?? 'grocery_store';

        if (!$uri) {
            throw new RuntimeException('MongoDB URI is not configured.');
        }

        self::$client = new Client($uri);
        self::$database = self::$client->selectDatabase($dbName);

        return self::$database;
    }

    public static function collection(string $name): \MongoDB\Collection
    {
        return self::db()->selectCollection($name);
    }

    private static function loadEnv(): void
    {
        if (self::$envLoaded) {
            return;
        }

        $root = dirname(__DIR__, 2);
        if (!file_exists($root . '/.env')) {
            throw new RuntimeException('.env file was not found.');
        }

        $dotenv = Dotenv::createImmutable($root);
        $dotenv->safeLoad();
        self::$envLoaded = true;
    }
}
