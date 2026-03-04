<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OrderRepositoryTest extends TestCase
{
    protected function setUp(): void { db()->beginTransaction(); }
    protected function tearDown(): void { db()->rollBack(); }

    public function testInsertOrderLinkedToClientAndUser(): void
    {
        // manager
        db()->prepare("INSERT INTO users(login, password_hash, role) VALUES(?,?,?)")
          ->execute(['test_manager', password_hash('123', PASSWORD_BCRYPT), 'manager']);
        $userId = (int)db()->lastInsertId();

        // client
        db()->prepare("INSERT INTO clients(full_name, phone, address, comment) VALUES(?,?,?,?)")
          ->execute(['Клиент 1', '70000000001', 'Адрес 1', null]);
        $clientId = (int)db()->lastInsertId();

        // order
        db()->prepare("
          INSERT INTO orders(client_id, manager_id, created_date, description, amount, planned_finish_date, status)
          VALUES (?,?,?,?,?,?,?)
        ")->execute([$clientId, $userId, '2026-01-01', 'Описание', 1000, '2026-01-10', 'new']);

        $orderId = (int)db()->lastInsertId();
        $this->assertGreaterThan(0, $orderId);

        $stmt = db()->prepare("SELECT client_id, manager_id FROM orders WHERE id=?");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();

        $this->assertSame($clientId, (int)$row['client_id']);
        $this->assertSame($userId, (int)$row['manager_id']);
    }
}