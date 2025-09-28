<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) { echo json_encode(['success'=>false]); exit; }
$table_id = intval($data['table_id']);
$items = $data['items'];

$pdo->beginTransaction();
try {
    $total = 0;
    foreach($items as $it) $total += $it['qty'] * $it['price'];
    $stmt = $pdo->prepare("INSERT INTO orders (table_id,total) VALUES (?,?)");
    $stmt->execute([$table_id, $total]);
    $order_id = $pdo->lastInsertId();
    $ins = $pdo->prepare("INSERT INTO order_items (order_id,menu_id,qty,price) VALUES (?,?,?,?)");
    foreach($items as $it){
        $ins->execute([$order_id, $it['menu_id'], $it['qty'], $it['price']]);
    }
    $pdo->commit();
    echo json_encode(['success'=>true,'order_id'=>$order_id]);
} catch (Exception $e){
    $pdo->rollBack();
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
