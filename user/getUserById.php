<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Memasukkan file konfigurasi database
include 'db.php';

// Ambil user_id dari parameter GET
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo json_encode(['error' => 'Invalid user_id']);
    exit();
}

$userId = intval($_GET['user_id']);

// Query untuk mendapatkan data pengguna
$sql = "SELECT user_id, name, date_of_birth, email, nohp, gender, photo_url FROM tb_users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Tentukan path lengkap gambar
    $uploadDir = 'http://192.168.1.8/apiMeowApp/user/path-to-profile-pictures/';
    $user['photo_url'] = $uploadDir . $user['photo_url'];
    
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>
