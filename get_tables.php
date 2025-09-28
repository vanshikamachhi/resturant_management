<?php
require_once '../db.php'; // adjust path if necessary: project root has db.php; here api/ is a subfolder
$stmt = $pdo->query("SELECT id,name FROM tables");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
