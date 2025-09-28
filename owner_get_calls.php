<?php
require_once '../db.php';
$stmt = $pdo->query("SELECT * FROM service_calls ORDER BY created_at DESC LIMIT 50");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
