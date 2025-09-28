<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$order_id = intval($data['order_id']);
$status = $data['status'];
$pd = $pdo->prepare("UPDATE orders SET status=? WHERE id=?");
$pd->execute([$status, $order_id]);
echo json_encode(['success'=>true]);
