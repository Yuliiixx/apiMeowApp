<?php
include 'db.php'; // Sertakan konfigurasi database

header('Content-Type: application/json'); // Menambahkan header untuk tipe konten JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Mengambil post_id dari parameter POST
$postId = $_POST['post_id'] ?? null;

if ($postId) {
    // Memulai transaksi
    $conn->begin_transaction();

    try {
        // Menghapus komentar terkait
        $query = "DELETE FROM tb_comments WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();

        // Menghapus like terkait
        $query = "DELETE FROM tb_likes WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();

        // Menghapus postingan
        $query = "DELETE FROM tb_posts WHERE post_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();

        // Menyelesaikan transaksi
        $conn->commit();

        echo json_encode(["success" => true]); // Mengirimkan respons JSON jika penghapusan berhasil
    } catch (Exception $e) {
        // Membatalkan transaksi jika terjadi kesalahan
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Gagal menghapus postingan: " . $e->getMessage()]); // Mengirimkan respons JSON jika terjadi kesalahan
    }
} else {
    echo json_encode(["success" => false, "message" => "ID postingan tidak ditemukan"]); // Mengirimkan respons JSON jika ID postingan tidak ditemukan
}

$conn->close(); // Menutup koneksi ke database
?>
