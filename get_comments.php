<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require 'db.php'; // Pastikan file ini berada di direktori yang benar

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Mendapatkan post_id dari parameter query string
    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

    if ($post_id > 0) {
        // Query untuk mengambil komentar berdasarkan post_id
        $query = "
        SELECT c.comment_id, c.comment, c.created_at, u.name AS user_name, u.photo_url AS user_photo_url
        FROM tb_comments c
        JOIN tb_users u ON c.user_id = u.user_id
        WHERE c.post_id = ?
        ORDER BY c.created_at ASC
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $comments = array();
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode($comments);
    } else {
        echo json_encode(array('error' => 'Invalid post ID'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request method'));
}
?>
