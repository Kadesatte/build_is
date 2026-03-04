<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DbConnectionTest extends TestCase
{
    public function testDbConnectionWorks(): void
    {
        $pdo = db();
        $this->assertInstanceOf(PDO::class, $pdo);

        $row = $pdo->query("SELECT 1 AS ok")->fetch();
        $this->assertSame('1', (string)$row['ok']);
    }
}