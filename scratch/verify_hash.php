<?php
$hash = password_hash('password123', PASSWORD_DEFAULT);
echo "Hash: $hash\n";
echo "Verify: " . (password_verify('password123', $hash) ? 'PASS' : 'FAIL') . "\n";
// Check current hash in DB
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=event_management', 'root', '');
$row = $pdo->query("SELECT password_hash FROM users WHERE email = 'manager@eo.com' LIMIT 1")->fetch();
if ($row) {
    echo "DB hash: " . $row['password_hash'] . "\n";
    echo "Verify DB hash: " . (password_verify('password123', $row['password_hash']) ? 'PASS' : 'FAIL') . "\n";
} else {
    echo "User not found in DB\n";
}
