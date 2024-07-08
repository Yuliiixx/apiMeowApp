<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include 'db.php';

// Cek metode request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid']);
    error_log('Metode request bukan POST');
    exit();
}

// Cek apakah file foto diunggah
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $errorMessage = isset($_FILES['photo']['error']) ? $_FILES['photo']['error'] : 'Foto tidak ditemukan atau kesalahan upload';
    echo json_encode(['status' => 'error', 'message' => $errorMessage]);
    error_log('Kesalahan upload foto: ' . $errorMessage);
    exit();
}

// Ambil data dari request
$userId = $_POST['user_id'];
$caption = $_POST['caption'];

// Validasi data
if (!isset($userId) || !isset($caption)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    error_log('User ID atau Caption tidak diset');
    exit();
}

// Verifikasi user_id ada di tb_users
$userCheckQuery = "SELECT * FROM tb_users WHERE user_id = ?";
$userCheckStmt = $conn->prepare($userCheckQuery);
$userCheckStmt->bind_param("i", $userId);
$userCheckStmt->execute();
$userCheckResult = $userCheckStmt->get_result();

if ($userCheckResult->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User ID tidak ditemukan']);
    error_log('User ID tidak ditemukan: ' . $userId);
    exit();
}

// Tentukan folder target dan nama file
$targetDir = 'images/';
$photoName = basename($_FILES['photo']['name']);
$targetFilePath = $targetDir . $photoName;

// Pindahkan file yang diunggah ke folder target
if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan file yang diunggah']);
    error_log('Gagal memindahkan file yang diunggah: ' . $_FILES['photo']['tmp_name'] . ' ke ' . $targetFilePath);
    exit();
}

// Masukkan data ke tb_posts
$photoUrl = $photoName; // Hanya menyimpan nama file foto
$query = "INSERT INTO tb_posts (user_id, photo_url, caption) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Query gagal: ' . $conn->error]); // Tambahkan info kesalahan
    error_log('Query gagal: ' . $conn->error);
    exit();
}
$stmt->bind_param("iss", $userId, $photoUrl, $caption);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Post berhasil ditambahkan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan post']);
    error_log('Gagal menambahkan post: ' . $stmt->error);
}

$stmt->close();
$conn->close();
?>
