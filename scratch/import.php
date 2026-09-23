<?php
try {
    $sql = file_get_contents(__DIR__ . '/../database/event_management.sql');
    $pdo = new PDO('mysql:host=localhost;port=3306', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec($sql);
    echo "SQL_IMPORT_SUCCESS\n";

    // Verify imported tables
    $pdo->query("USE event_management");
    $users = $pdo->query("SELECT id, name, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
    echo "Users count: " . count($users) . "\n";
    print_r($users);

    $events = $pdo->query("SELECT id, title, status, estimated_guests FROM events")->fetchAll(PDO::FETCH_ASSOC);
    echo "Events count: " . count($events) . "\n";
    print_r($events);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
