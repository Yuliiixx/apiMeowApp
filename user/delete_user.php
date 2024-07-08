<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];

    $sql = "DELETE FROM tb_users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        echo json_encode(["error" => "Deletion failed: " . $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>
