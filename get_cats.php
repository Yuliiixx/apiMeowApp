

<?php

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include 'db.php'; // Include konfigurasi database

$query = "SELECT * FROM tb_cats";
$result = $conn->query($query);

if ($result === false) {
    echo json_encode(['error' => 'Failed to query the database: ' . $conn->error]);
    error_log('SQL Error: ' . $conn->error);
    exit;
}

$cats = [];
while ($row = $result->fetch_assoc()) {
    $row['profile_picture_url'] = 'http://192.168.1.8/apiMeowApp/images/' . $row['profile_picture_url'];
    $cats[] = $row;
}

echo json_encode($cats);
$conn->close();
?>