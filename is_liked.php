<?php
include 'db.php'; // Include konfigurasi database

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Cek apakah 'post_id' dan 'user_id' ada di $_POST
if (isset($_POST['post_id']) && isset($_POST['user_id'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_POST['user_id'];

    $sql = "SELECT 1 FROM tb_likes WHERE post_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $liked = $result->num_rows > 0;

    echo json_encode(['liked' => $liked]);

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing post_id or user_id']);
}

$conn->close();
?>
