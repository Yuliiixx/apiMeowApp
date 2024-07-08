<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $email = $_POST['email'];
    $nohp = $_POST['nohp'];
    $gender = $_POST['gender'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO tb_users (name, date_of_birth, email, nohp, gender, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $date_of_birth, $email, $nohp, $gender, $password);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Registration successful"]);
    } else {
        echo json_encode(["error" => "Registration failed: " . $stmt->error]);
    }
    $stmt->close();
}
$conn->close();
?>
