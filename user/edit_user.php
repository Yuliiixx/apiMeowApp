<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Memasukkan file konfigurasi database
include 'db.php';

// Ambil data dari request
$userId = $_POST['user_id'];
$name = $_POST['name'];
$dateOfBirth = $_POST['date_of_birth'];
$email = $_POST['email'];
$nohp = $_POST['nohp'];
$gender = $_POST['gender'];

// Menyiapkan query untuk update data pengguna
$sql = "UPDATE tb_users SET name = ?, date_of_birth = ?, email = ?, nohp = ?, gender = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssi', $name, $dateOfBirth, $email, $nohp, $gender, $userId);

if ($stmt->execute()) {
    // Cek apakah ada file gambar yang diupload
    if (isset($_FILES['photo_url']) && $_FILES['photo_url']['error'] === UPLOAD_ERR_OK) {
        $profileImage = $_FILES['photo_url']['tmp_name'];
        $profileImageName = 'profile_'.$userId.'.jpg';
        $uploadDir = 'path-to-profile-pictures/';
        $uploadFile = $uploadDir.$profileImageName;

        // Pastikan direktori upload ada
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Pindahkan file gambar ke direktori yang ditentukan
        if (move_uploaded_file($profileImage, $uploadFile)) {
            // Update URL gambar profil di database
            $photoUrl = $profileImageName;
            $updatePhotoUrl = "UPDATE tb_users SET photo_url = ? WHERE user_id = ?";
            $stmtPhoto = $conn->prepare($updatePhotoUrl);
            $stmtPhoto->bind_param('si', $photoUrl, $userId);
            $stmtPhoto->execute();
            $stmtPhoto->close();
        } else {
            echo json_encode(['error' => 'Failed to move uploaded file']);
            exit();
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Update failed']);
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>
