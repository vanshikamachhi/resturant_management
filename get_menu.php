<?php
require_once '../db.php';
$stmt = $pdo->query("SELECT id,name,description,price,category FROM menu_items");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
