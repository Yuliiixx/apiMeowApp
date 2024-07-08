<?php
include 'db.php'; // Include konfigurasi database

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Cek apakah 'post_id' dan 'user_id' ada di $_POST
if (isset($_POST['post_id']) && isset($_POST['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    // Validasi input
    if (!is_numeric($post_id) || !is_numeric($user_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid post_id or user_id']);
        exit;
    }

    $sql = "INSERT INTO tb_likes (post_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }

    $stmt->close();
} else {
    $missing_params = [];
    if (!isset($_POST['post_id'])) {
        $missing_params[] = 'post_id';
    }
    if (!isset($_POST['user_id'])) {
        $missing_params[] = 'user_id';
    }
    echo json_encode(['status' => 'error', 'message' => 'Missing ' . implode(', ', $missing_params)]);
}

$conn->close();
?>
