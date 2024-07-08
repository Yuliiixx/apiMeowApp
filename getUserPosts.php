<?php
include 'db.php'; // Sertakan konfigurasi database

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// URL base untuk gambar
$baseUrl = 'http://192.168.1.8/apiMeowApp/images';

// Pastikan request adalah GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Ambil user_id dari query parameter
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    // Cek apakah user_id valid
    if ($user_id > 0) {
        $sql = "
          SELECT p.post_id, p.user_id, CONCAT('$baseUrl/', p.photo_url) AS post_photo_url, p.caption, p.created_at,
                 u.name AS user_name, CONCAT('$baseUrl/', u.photo_url) AS user_photo_url,
                 (SELECT COUNT(*) FROM tb_likes WHERE post_id = p.post_id) AS like_count,
                 (SELECT COUNT(*) FROM tb_comments WHERE post_id = p.post_id) AS comment_count
          FROM tb_posts p
          JOIN tb_users u ON p.user_id = u.user_id
          WHERE p.user_id = ?
          ORDER BY p.created_at DESC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $posts = [];

        while ($row = $result->fetch_assoc()) {
            // Cek jika nilai null, ubah menjadi nilai default
            $row['like_count'] = isset($row['like_count']) ? (int) $row['like_count'] : 0;
            $row['comment_count'] = isset($row['comment_count']) ? (int) $row['comment_count'] : 0;

            $posts[] = $row;
        }

        echo json_encode($posts);
        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid user_id"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

// Tutup koneksi database
$conn->close();
?>
