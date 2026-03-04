<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ClientRepositoryTest extends TestCase
{
    protected function setUp(): void { db()->beginTransaction(); }
    protected function tearDown(): void { db()->rollBack(); }

    public function testInsertClient(): void
    {
        db()->prepare("INSERT INTO clients(full_name, phone, address, comment) VALUES(?,?,?,?)")
           ->execute(['Тест Клиент', '70000000000', 'Тест адрес', 'коммент']);

        $id = (int)db()->lastInsertId();
        $this->assertGreaterThan(0, $id);

        $stmt = db()->prepare("SELECT full_name FROM clients WHERE id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        $this->assertSame('Тест Клиент', $row['full_name']);
    }
}