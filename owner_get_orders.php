<?php
require_once '../db.php';
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 50");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
$out = [];
foreach($orders as $o){
  $itStmt = $pdo->prepare("SELECT oi.qty, oi.price, m.name FROM order_items oi JOIN menu_items m ON m.id=oi.menu_id WHERE oi.order_id=?");
  $itStmt->execute([$o['id']]);
  $items = $itStmt->fetchAll(PDO::FETCH_ASSOC);
  $o['items'] = $items;
  $out[] = $o;
}
echo json_encode($out);
