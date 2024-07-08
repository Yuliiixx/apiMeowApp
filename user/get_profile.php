<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Include file koneksi database
include 'db.php';

// Pastikan request adalah GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Ambil user_id dari query parameter
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

    // Cek apakah user_id valid
    if ($user_id > 0) {
        $sql = "SELECT user_id, name, date_of_birth, email, nohp, gender FROM tb_users WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Cek apakah user ditemukan
        if ($user) {
            echo json_encode($user);
        } else {
            echo json_encode(["error" => "User not found"]);
        }
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
