<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$table_id = intval($data['table_id']);
$note = substr($data['note'] ?? '', 0, 250);

$stmt = $pdo->prepare("INSERT INTO service_calls (table_id, note) VALUES (?, ?)");
$stmt->execute([$table_id, $note]);
echo json_encode(['success'=>true]);
