<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$pid = intval($data['payment_id']);

// Mark payment completed
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("UPDATE payments SET status='completed' WHERE id=?");
    $stmt->execute([$pid]);

    // Create an order related to this payment (demo)
    $stmt = $pdo->prepare("INSERT INTO orders (table_id,total,status) VALUES (NULL, ?, 'paid')");
    // for demo we find amount
    $amount = $pdo->query("SELECT amount FROM payments WHERE id=".$pid)->fetchColumn();
    $stmt->execute([$amount]);
    $order_id = $pdo->lastInsertId();

    // link payment to order
    $pdo->prepare("UPDATE payments SET order_id=? WHERE id=?")->execute([$order_id, $pid]);

    $pdo->commit();
    echo json_encode(['success'=>true,'order_id'=>$order_id]);
} catch (Exception $e){
    $pdo->rollBack();
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
