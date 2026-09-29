<?php
declare(strict_types=1);

namespace Src\Core;

use PDO;
use Src\Core\Database;

class Transaction
{
    private static ?PDO $conn = null;

    public static function open(): void
    {
        self::$conn = Container::get(Database::class)->getConnection();
        self::$conn->beginTransaction();
    }

    public static function rollback(): void
    {
        if( self::$conn) {
            self::$conn->rollBack();
            self::$conn = null;
        }
    }

    public static function close(): void
    {
        if (self::$conn) {
            self::$conn->commit();
            self::$conn = null;
        }
    }

    public static function run(callable $work, ?string $errorMessage = null): mixed
    {
        self::open();

        try {
            $result = $work();
            self::close();

            return $result;
        } catch (\Throwable $e) {
            self::rollback();

            if ($errorMessage !== null) {
                throw new \RuntimeException($errorMessage . ': ' . $e->getMessage(), previous: $e);
            }

            throw $e;
        }
    }
}
