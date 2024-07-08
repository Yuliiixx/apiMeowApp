<?php
include 'db.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$baseUrl = 'http://192.168.1.8/apiMeowApp/images/'; // Tambahkan URL dasar untuk gambar

$sql = "SELECT * FROM tb_articles";
$result = $conn->query($sql);

$articles = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if (!empty($row['image'])) {
            $row['image'] = $baseUrl . $row['image']; // Tambahkan URL dasar ke path gambar
        }
        $articles[] = $row;
    }
}

echo json_encode($articles);

$conn->close();
?>
