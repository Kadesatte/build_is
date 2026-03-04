<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CreateClientOrderFlowTest extends TestCase
{
    protected function setUp(): void { db()->beginTransaction(); }
    protected function tearDown(): void { db()->rollBack(); }

    public function testFullFlowClientOrderTechSpecReport(): void
    {
        // user
        db()->prepare("INSERT INTO users(login, password_hash, role) VALUES(?,?,?)")
           ->execute(['mgr_flow', password_hash('123', PASSWORD_BCRYPT), 'manager']);
        $userId = (int)db()->lastInsertId();

        // client
        db()->prepare("INSERT INTO clients(full_name, phone, address, comment) VALUES(?,?,?,?)")
           ->execute(['Flow Client', '70000000002', 'Flow Address', null]);
        $clientId = (int)db()->lastInsertId();

        // order
        db()->prepare("
          INSERT INTO orders(client_id, manager_id, created_date, description, amount, planned_finish_date, status)
          VALUES (?,?,?,?,?,?,?)
        ")->execute([$clientId, $userId, '2026-01-01', 'Flow order', 5000, '2026-01-20', 'in_progress']);
        $orderId = (int)db()->lastInsertId();

        // tech spec
        db()->prepare("INSERT INTO tech_specs(order_id, title, content, created_date) VALUES(?,?,?,?)")
           ->execute([$orderId, 'ТЗ', 'Сделать ремонт', '2026-01-02']);

        // report
        db()->prepare("INSERT INTO work_reports(order_id, report_date, work_done, comment) VALUES(?,?,?,?)")
           ->execute([$orderId, '2026-01-03', 'Демонтаж выполнен', 'ок']);

        // asserts
        $ts = db()->prepare("SELECT order_id FROM tech_specs WHERE order_id=?");
        $ts->execute([$orderId]);
        $this->assertNotFalse($ts->fetch());

        $wr = db()->prepare("SELECT COUNT(*) AS cnt FROM work_reports WHERE order_id=?");
        $wr->execute([$orderId]);
        $row = $wr->fetch();

        $this->assertSame('1', (string)$row['cnt']);
    }
}