<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$amount = floatval($data['amount']);
$table_id = intval($data['table_id']);

// Create a payment record (mock)
$stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, method, status) VALUES (NULL, ?, ?, ?)");
$stmt->execute([$amount, 'mock', 'pending']);
$pid = $pdo->lastInsertId();

// Return payment id to client — normally you'd redirect to gateway
echo json_encode(['success'=>true,'payment_id'=>$pid]);
