<?php
require_once '../db.php';
$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);
$pdo->prepare("UPDATE service_calls SET handled=1 WHERE id=?")->execute([$id]);
echo json_encode(['success'=>true]);
