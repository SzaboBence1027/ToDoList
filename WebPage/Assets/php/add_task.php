<?php
require_once 'connect.php';
session_start();
header('Content-Type: application/json');
function CheckForExistingTask($pdo, $user_id, $t_name, $deadline)
{
    $stmt = $pdo->prepare('SELECT * FROM task WHERE user_id = :user_id AND t_name = :t_name AND deadline = :deadline AND progresson = 0');
    $stmt->execute(['user_id' => $user_id, 't_name' => $t_name, 'deadline' => $deadline]);
    return $stmt->fetch() ? true : false;
}
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Felhasználó azonosító szükséges']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$t_name = $_POST['t_name'] ?? null;
$description = $_POST['description'] ?? null;
$label_id = isset($_POST['label_id']) && $_POST['label_id'] !== '' ? intval($_POST['label_id']) : 1; // Default to "No Label" ID
$priority = $_POST['priority'] ?? null;
$progresson = $_POST['progresson'] ?? null;
$deadline = $_POST['deadline'] ?? null;




if (!$t_name || !$deadline) {
    echo json_encode(['success' => false, 'message' => 'Feladat név és dátum szükséges']);
    exit;
}
if (CheckForExistingTask($pdo, $user_id, $t_name, $deadline)) {
    echo json_encode(['success' => false, 'message' => 'Ilyen nevű feladat már létezik ezen a napon']);
    exit;
}
try {
    $stmt = $pdo->prepare('INSERT INTO task (user_id, t_name, description, label_id, priority, progresson, deadline) VALUES (:user_id, :t_name, :description, :label_id, :priority, :progresson, :deadline)');
    $stmt->execute([
        'user_id' => $user_id,
        't_name' => $t_name,
        'description' => $description,
        'label_id' => $label_id, // Default to "No Label" ID if no label is provided
        'priority' => $priority,
        'progresson' => $progresson,
        'deadline' => $deadline
    ]);

    echo json_encode(['success' => true, 'message' => 'Feladat sikeresen hozzáadva ']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Query failed: ' . $e->getMessage()]);
}
?>